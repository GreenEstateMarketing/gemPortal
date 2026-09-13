<?php

namespace Botble\RealEstate\Services;

use Botble\Location\Models\City;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Member;
use Botble\RealEstate\Models\Project;
use Botble\RealEstate\Models\Property;
use Illuminate\Support\Arr;

class PropertySubmissionService
{
    /**
     * Create the first draft row for a brand new "Submit Ad" journey.
     *
     * This early INSERT is what makes per-step persistence, resuming later,
     * and features()/facilities() syncing possible - those need a real
     * property id, and a user can otherwise abandon the wizard after any step.
     */
    public function startDraft(array $context): Property
    {
        return Property::create([
            'name' => 'Untitled draft',
            'type' => 'sale',
            'status' => 'selling',
            'moderation_status' => 'pending',
            'submission_status' => 'draft',
            'wizard_step' => 0,
            'wizard_role' => $context['role'],
            'images' => '[]',
            'documents' => '[]',
            'last_wizard_activity_at' => now(),
            'author_id' => $context['author_id'] ?? null,
            'author_type' => $context['author_type'] ?? Member::class,
            'member_id' => $context['member_id'] ?? null,
        ]);
    }

    /**
     * Save the "Basics & Price" step (sub-step 1).
     */
    public function saveBasics(Property $property, array $data): Property
    {
        $status = Arr::get($data, 'type') === 'rent' ? 'renting' : 'selling';

        $square = Arr::get($data, 'square');
        $areaUnit = Arr::pull($data, 'area_units');
        if ($square !== null && $areaUnit) {
            $data['square'] = getSqFeet(str_replace(',', '', $square), $areaUnit);
        }

        $previousProjectId = (int) $property->project_id;

        $property->fill(Arr::only($data, [
            'name', 'type', 'category_id', 'project_id', 'description',
            'price', 'currency_id', 'price_unit', 'square',
            'number_bedroom', 'number_bathroom', 'number_floor', 'built_in',
        ]));
        $property->project_id = $property->project_id ?: 0;
        $property->status = $status;

        // Picking a (different) project is a strong signal of where the
        // property actually is - default its location to the project's
        // own, so the Location step opens pre-filled instead of blank.
        // Only triggers when the project selection actually changes, so it
        // doesn't clobber location details already customized on a later
        // revisit to this step with the same project still selected.
        if ($property->project_id && $property->project_id !== $previousProjectId) {
            $this->copyLocationFromProject($property);
        }

        return $this->markStepComplete($property, 1);
    }

    /**
     * Copies a project's own location (city/area/address/coordinates) onto
     * the property, deriving state/country from the city since Project
     * itself only stores city_id/city_area_id.
     */
    protected function copyLocationFromProject(Property $property): void
    {
        $project = Project::find($property->project_id);

        if (!$project) {
            return;
        }

        $property->city_id = $project->city_id;
        $property->city_area_id = $project->city_area_id;
        $property->location = $project->location;
        $property->latitude = $project->latitude;
        $property->longitude = $project->longitude;

        if ($project->city_id && ($city = City::find($project->city_id))) {
            $property->state_id = $city->state_id;
            $property->country_id = $city->country_id;
        }
    }

    /**
     * Save the "Location & Details" step (sub-step 2), including features and facilities.
     */
    public function saveLocation(Property $property, array $data, SaveFacilitiesService $saveFacilitiesService): Property
    {
        $property->fill(Arr::only($data, [
            'country_id', 'state_id', 'city_id', 'city_area_id',
            'location', 'latitude', 'longitude',
        ]));
        $property->save();

        $property->features()->sync(Arr::get($data, 'features', []));
        $saveFacilitiesService->execute($property, Arr::get($data, 'facilities', []));

        return $this->markStepComplete($property, 2);
    }

    /**
     * Save the "Media & Documents" step (sub-step 3).
     *
     * Images/documents are uploaded ahead of time via the existing media
     * upload endpoints (the same AJAX-upload-then-store-URL pattern the old
     * image gallery already used) - by the time this runs, $data['images']
     * and $data['documents'] are just JSON arrays of already-hosted URLs, so
     * no server-side file handling is needed here.
     */
    public function saveMedia(Property $property, array $data): Property
    {
        $property->images = json_encode(Arr::get($data, 'images', []));
        $property->documents = json_encode(Arr::get($data, 'documents', []));
        $property->auto_renew = (bool) Arr::get($data, 'auto_renew', false);
        $property->never_expired = (bool) Arr::get($data, 'never_expired', false);

        if (array_key_exists('is_featured', $data)) {
            $property->is_featured = (bool) $data['is_featured'];
        }

        if (array_key_exists('moderation_status', $data)) {
            $property->moderation_status = $data['moderation_status'];
            // Only keep a reason around while the status is actually
            // "rejected" - stale text shouldn't linger once it's cleared.
            $property->reject_reason = $data['moderation_status'] === 'rejected'
                ? Arr::get($data, 'reject_reason')
                : null;
        }

        return $this->markStepComplete($property, 3);
    }

    /**
     * Reached the Review sub-step (nothing to persist beyond the cursor).
     */
    public function markReviewReached(Property $property): Property
    {
        return $this->markStepComplete($property, 4);
    }

    /**
     * Final "Submit" action on the Review sub-step.
     *
     * Deliberately does not touch moderation_status here: a brand new draft
     * already defaults to "pending" at creation, and an edit of an
     * already-approved property must never be silently reset back to
     * pending just because the owner re-submitted the wizard.
     */
    public function finalize(Property $property): Property
    {
        if (! $property->expire_date) {
            $property->expire_date = now()->addDays(
                config('plugins.real-estate.real-estate.property_expired_after_x_days')
            );
        }

        $property->submission_status = 'submitted';
        $property->wizard_step = 5;
        $property->last_wizard_activity_at = now();
        $property->save();

        return $property;
    }

    /**
     * Re-point a guest's session-owned draft to the member they just
     * authenticated as (existing member) or that was just created for them
     * (new signup), so the draft becomes visible in their dashboard.
     */
    public function claimGuestDraftForMember(Property $property, Member $member): Property
    {
        $property->member_id = $member->id;

        if (! $property->author_id) {
            $property->author_type = Member::class;
        }

        $property->save();

        return $property;
    }

    protected function markStepComplete(Property $property, int $step): Property
    {
        $property->wizard_step = max((int) $property->wizard_step, $step);
        $property->last_wizard_activity_at = now();
        $property->save();

        return $property;
    }
}
