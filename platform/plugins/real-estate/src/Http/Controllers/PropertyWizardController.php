<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Location\Models\Country;
use Botble\RealEstate\Http\Requests\PropertyWizardAgentStepRequest;
use Botble\RealEstate\Http\Requests\PropertyWizardBasicsStepRequest;
use Botble\RealEstate\Http\Requests\PropertyWizardLocationStepRequest;
use Botble\RealEstate\Http\Requests\PropertyWizardMediaStepRequest;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Category;
use Botble\RealEstate\Models\CategoryDocument;
use Botble\RealEstate\Models\Currency;
use Botble\RealEstate\Models\Facility;
use Botble\RealEstate\Models\Feature;
use Botble\RealEstate\Models\Member;
use Botble\RealEstate\Models\Project;
use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Services\PropertySubmissionService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use SeoHelper;
use Theme;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PropertyWizardController extends Controller
{
    /**
     * @var PropertySubmissionService
     */
    protected $service;

    public function __construct(PropertySubmissionService $service)
    {
        $this->service = $service;
    }

    /**
     * Load the wizard: resumes an in-progress draft, opens an existing
     * property for edit, or starts a brand new draft.
     */
    public function show(Request $request, ?Property $property = null)
    {
        $role = $this->currentRole($request);

        if ($role === 'guest' && ! $property) {
            if (auth('member')->check()) {
                return redirect()->route('public.member.properties.wizard.show');
            }

            if (auth('account')->check()) {
                return redirect()->route('public.account.properties.wizard.show');
            }
        }

        if ($role === 'admin') {
            abort_unless(
                auth()->user() && auth()->user()->hasPermission($property ? 'property.edit' : 'property.create'),
                403
            );
        }

        $property = $this->resolveForShow($role, $property);

        // Opening an already-submitted property should land wherever it
        // actually left off in the overall journey (Choose Agent, or Ad
        // Verification once an agent's assigned) rather than always
        // restarting at Submit Ad's first step - unless a specific step
        // was explicitly requested (e.g. the global header's "Submit Ad"
        // link, or deliberately revisiting a step to edit it).
        if ($property->isSubmitted() && !$request->has('step')) {
            return redirect()->route(
                $this->routeName($role, $this->hasAssignedAgent($property) ? 'ad-verification' : 'choose-agent'),
                ['property' => $property->id]
            );
        }

        $furthestReachable = $property->isSubmitted() ? 4 : min(4, ((int) $property->wizard_step) + 1);

        $activeStep = (int) $request->query('step', $this->defaultStepFor($property));
        $activeStep = max(1, min($furthestReachable, $activeStep));

        if ($activeStep === 4) {
            $property = $this->service->markReviewReached($property);
        }

        $showBaseUrl = route($this->routeName($role, 'show'), ['property' => $property->id]);

        $data = [
            'role' => $role,
            'property' => $property,
            'activeStep' => $activeStep,
            'furthestReachable' => $furthestReachable,
            'wizardContext' => $this->wizardContext($role),
            'isGated' => $property->isDraft(),
            'showBaseUrl' => $showBaseUrl,
            'stepUrls' => [
                'basics' => route($this->routeName($role, 'basics'), ['property' => $property->id]),
                'location' => route($this->routeName($role, 'location'), ['property' => $property->id]),
                'media' => route($this->routeName($role, 'media'), ['property' => $property->id]),
                'finalize' => route($this->routeName($role, 'finalize'), ['property' => $property->id]),
            ],
            'chooseAgentUrl' => $property->isSubmitted()
                ? route($this->routeName($role, 'choose-agent'), ['property' => $property->id])
                : null,
            'adVerificationUrl' => $this->hasAssignedAgent($property)
                ? route($this->routeName($role, 'ad-verification'), ['property' => $property->id])
                : null,
            'uploadUrl' => $this->uploadUrlFor($role),
            'authenticateUrl' => $role === 'guest' ? route('general-property-wizard.authenticate') : null,
            'categories' => Category::where('status', BaseStatusEnum::PUBLISHED)->orderBy('name')->get(['id', 'name', 'parent_id']),
            // Project has no draft/published concept of its own (its 'status'
            // column is a sale/rent lifecycle state, not visibility), so
            // every project is a valid choice here.
            'projects' => Project::orderBy('name')->get(['id', 'name']),
            'currencies' => Currency::orderBy('order')->get(['id', 'title', 'symbol']),
            'countries' => Country::where('status', BaseStatusEnum::PUBLISHED)->orderBy('name')->get(['id', 'name']),
            'features' => Feature::where('status', BaseStatusEnum::PUBLISHED)->orderBy('name')->get(['id', 'name']),
            'facilities' => Facility::where('status', BaseStatusEnum::PUBLISHED)->orderBy('name')->get(['id', 'name']),
            // Which document types the property's chosen category requires
            // (set up by admins in Real Estate > Category Documents) - the
            // media step turns each of these into its own upload slot.
            'categoryDocuments' => $property->category_id
                ? CategoryDocument::with('document')->where('category_id', $property->category_id)->orderBy('id')->get()
                : collect(),
        ];

        if ($role === 'guest') {
            // Guests get the real site theme (header/footer/assets), rendered
            // through Theme::scope() so the theme's normal asset-registration
            // events fire - Theme::partial() alone does not trigger them.
            SeoHelper::setTitle(__('Add Property'));

            return Theme::scope(
                'real-estate.property-wizard',
                $data,
                'plugins/real-estate::wizard.index'
            )->render();
        }

        $wrapperViews = [
            'admin' => 'plugins/real-estate::admin.wizard.show',
            'agent' => 'plugins/real-estate::account.wizard.show',
            'member' => 'plugins/real-estate::member.wizard.show',
        ];

        return view($wrapperViews[$role], $data);
    }

    protected function uploadUrlFor(string $role): string
    {
        switch ($role) {
            case 'admin':
                return route('media.files.upload');
            case 'agent':
                return route('public.account.upload');
            default:
                return route('public.member.upload');
        }
    }

    public function saveBasics(PropertyWizardBasicsStepRequest $request, Property $property)
    {
        $role = $this->currentRole($request);
        $this->authorizeAccess($role, $property);

        $property = $this->service->saveBasics($property, $request->validated());

        return $this->stepSavedResponse($role, $property, 2);
    }

    public function saveLocation(PropertyWizardLocationStepRequest $request, Property $property)
    {
        $role = $this->currentRole($request);
        $this->authorizeAccess($role, $property);

        $property = $this->service->saveLocation(
            $property,
            $request->validated(),
            app(\Botble\RealEstate\Services\SaveFacilitiesService::class)
        );

        return $this->stepSavedResponse($role, $property, 3);
    }

    public function saveMedia(PropertyWizardMediaStepRequest $request, Property $property)
    {
        $role = $this->currentRole($request);
        $this->authorizeAccess($role, $property);

        $data = $request->validated();
        $context = $this->wizardContext($role);

        if (! $context['can']['setFeatured']) {
            unset($data['is_featured']);
        }

        if (! $context['can']['setModerationStatus']) {
            unset($data['moderation_status']);
            unset($data['reject_reason']);
        }

        $property = $this->service->saveMedia($property, $data);

        return $this->stepSavedResponse($role, $property, 4);
    }

    public function finalize(Request $request, Property $property)
    {
        $role = $this->currentRole($request);
        $this->authorizeAccess($role, $property);

        if ($role === 'guest' && ! $property->member_id) {
            return response()->json([
                'success' => false,
                'require_auth' => true,
            ], 422);
        }

        $property = $this->service->finalize($property);

        return response()->json([
            'success' => true,
            'redirect_url' => route($this->routeName($role, 'choose-agent'), ['property' => $property->id]),
        ]);
    }

    /**
     * The guest-only "log in or sign up" gate at Review time, reusing the
     * same existing-member/new-member validation the old single-form guest
     * flow already relied on.
     */
    public function authenticateGuest(Request $request)
    {
        $sessionId = session('wizard_draft_id');
        abort_unless($sessionId, 403);

        $property = Property::findOrFail($sessionId);

        if ($request->input('member_status') === 'new_user') {
            $validator = Validator::make($request->all(), [
                'full_name' => ['required', 'string', 'min:3', 'max:100', 'regex:/^[a-zA-Z]{3,}(?: [a-zA-Z]+){0,2}$/'],
                'new_email' => 'required|email|string|unique:members,email',
                'mobile_number' => ['required', 'regex:/^\+?[1-9][0-9]{7,14}$/'],
                'new_password' => 'required|min:6',
                'terms' => 'required|accepted',
            ], [
                'terms.required' => 'Please accept the terms & conditions.',
                'mobile_number.regex' => 'The phone number format is invalid. It must be a valid international number, e.g., +1234567890.',
                'new_email.unique' => 'An account with this email already exists.',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $member = Member::create([
                'full_name' => $request->input('full_name'),
                'email' => $request->input('new_email'),
                'mobile_no' => $request->input('mobile_number'),
                'password' => Hash::make($request->input('new_password')),
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|string',
                'password' => 'required|min:6',
                'terms' => 'required|accepted',
            ], [
                'terms.required' => 'Please accept the terms & conditions.',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $member = Member::where('email', $request->input('email'))->first();

            if (! $member || ! Hash::check($request->input('password'), $member->password)) {
                return response()->json([
                    'success' => false,
                    'errors' => ['email' => ['Invalid email or password.']],
                ], 422);
            }
        }

        $this->service->claimGuestDraftForMember($property, $member);
        $property = $this->service->finalize($property);

        Auth::guard('member')->login($member);

        session()->forget('wizard_draft_id');

        return response()->json([
            'success' => true,
            'redirect_url' => route('public.member.properties.wizard.choose-agent', ['property' => $property->id]),
        ]);
    }

    public function chooseAgentPlaceholder(Request $request, Property $property)
    {
        $role = $this->currentRole($request);
        $this->authorizeAccess($role, $property);

        return view('plugins/real-estate::wizard.choose-agent-placeholder', [
            'role' => $role,
            'property' => $property,
            'chooseAgentUrl' => route($this->routeName($role, 'choose-agent'), ['property' => $property->id]),
            'saveAgentUrl' => route($this->routeName($role, 'save-agent'), ['property' => $property->id]),
            'showBaseUrl' => route($this->routeName($role, 'show'), ['property' => $property->id]),
            'adVerificationUrl' => $this->hasAssignedAgent($property)
                ? route($this->routeName($role, 'ad-verification'), ['property' => $property->id])
                : null,
            'nearbyAgents' => $this->nearbyAgentsFor($property),
        ]);
    }

    public function saveAgent(PropertyWizardAgentStepRequest $request, Property $property)
    {
        $role = $this->currentRole($request);
        $this->authorizeAccess($role, $property);

        $property->author_id = $request->validated()['agent_id'];
        $property->author_type = Account::class;
        $property->save();

        return response()->json([
            'success' => true,
            'next_url' => route($this->routeName($role, 'ad-verification'), ['property' => $property->id]),
        ]);
    }

    /**
     * Ad Verification (global step 3) - unlocked once an agent is assigned.
     * Just a placeholder screen for now.
     */
    public function adVerificationPlaceholder(Request $request, Property $property)
    {
        $role = $this->currentRole($request);
        $this->authorizeAccess($role, $property);

        if (!$this->hasAssignedAgent($property)) {
            return redirect()->route($this->routeName($role, 'choose-agent'), ['property' => $property->id]);
        }

        return view('plugins/real-estate::wizard.ad-verification-placeholder', [
            'role' => $role,
            'property' => $property,
            'chooseAgentUrl' => route($this->routeName($role, 'choose-agent'), ['property' => $property->id]),
            'showBaseUrl' => route($this->routeName($role, 'show'), ['property' => $property->id]),
        ]);
    }

    /**
     * Whether this property has an agent (as opposed to an admin/member)
     * assigned as its author - i.e. whether the Choose Agent step is done.
     */
    protected function hasAssignedAgent(Property $property): bool
    {
        return $property->author_type === Account::class && (bool) $property->author_id;
    }

    /**
     * Agents eligible for this property: whichever agents' drawn coverage
     * area contains its location, plus whichever agent is already assigned
     * (kept visible even if they no longer match, e.g. after the property's
     * location was edited).
     */
    protected function nearbyAgentsFor(Property $property)
    {
        $agents = $property->latitude && $property->longitude
            ? Account::query()->coveringPoint($property->longitude, $property->latitude)->get()
            : collect();

        if ($this->hasAssignedAgent($property) && !$agents->contains('id', $property->author_id)) {
            $existing = Account::find($property->author_id);

            if ($existing) {
                $agents->push($existing);
            }
        }

        return $agents;
    }

    protected function stepSavedResponse(string $role, Property $property, int $nextStep)
    {
        return response()->json([
            'success' => true,
            'wizard_step' => $property->wizard_step,
            'next_url' => route($this->routeName($role, 'show'), ['property' => $property->id]) . '?step=' . $nextStep,
        ]);
    }

    /**
     * The role is derived from the current route's name rather than taken as
     * a route parameter: mixing a plain scalar parameter with a route-model
     * bound parameter confuses Laravel 8's positional controller-argument
     * resolution (a route ->defaults() value is not part of the route's
     * bound parameters, so it does not reliably land in the right
     * constructor argument slot). Each of the 4 route groups this
     * controller is mounted under already has a distinctive name prefix.
     */
    protected function currentRole(Request $request): string
    {
        $name = optional($request->route())->getName() ?? '';

        if (strpos($name, 'public.account.') === 0) {
            return 'agent';
        }

        if (strpos($name, 'public.member.') === 0) {
            return 'member';
        }

        if (strpos($name, 'general-property-wizard') === 0) {
            return 'guest';
        }

        return 'admin';
    }

    /**
     * Each role owns its own route names (matching the existing per-role
     * naming conventions in routes/web.php); this maps a role + logical
     * action name to the concrete route name for that role.
     */
    protected function routeName(string $role, string $action): string
    {
        $map = [
            'admin' => 'property.wizard.' . $action,
            'agent' => 'public.account.properties.wizard.' . $action,
            'member' => 'public.member.properties.wizard.' . $action,
            'guest' => 'general-property-wizard.' . $action,
        ];

        return $map[$role] ?? $map['guest'];
    }

    protected function resolveForShow(string $role, ?Property $property): Property
    {
        if ($property && $role === 'guest' && (int) session('wizard_draft_id') !== (int) $property->id) {
            // A guest's "ownership" is only tracked for the lifetime of their
            // session (no login). A mismatch here just means a stale/expired
            // link, not an access violation - fall through to their current
            // draft (or a new one) instead of hard-erroring on a GET request.
            $property = null;
        }

        if ($property) {
            $this->authorizeAccess($role, $property);

            return $property;
        }

        if ($role === 'guest') {
            $sessionId = session('wizard_draft_id');

            if ($sessionId && ($existing = Property::find($sessionId))) {
                return $existing;
            }

            $property = $this->service->startDraft($this->contextFor($role));
            session(['wizard_draft_id' => $property->id]);

            return $property;
        }

        if (in_array($role, ['member', 'agent'], true)) {
            $existingDraft = $this->ownedQuery($role)->where('submission_status', 'draft')->latest('id')->first();

            if ($existingDraft) {
                return $existingDraft;
            }
        }

        return $this->service->startDraft($this->contextFor($role));
    }

    protected function contextFor(string $role): array
    {
        switch ($role) {
            case 'agent':
                return [
                    'role' => 'agent',
                    'author_id' => auth('account')->id(),
                    'author_type' => Account::class,
                ];
            case 'member':
                return [
                    'role' => 'member',
                    'member_id' => auth('member')->id(),
                    'author_type' => Member::class,
                ];
            case 'admin':
                return [
                    'role' => 'admin',
                    'author_type' => Account::class,
                ];
            default:
                return [
                    'role' => 'guest',
                    'author_type' => Member::class,
                ];
        }
    }

    protected function ownedQuery(string $role)
    {
        if ($role === 'agent') {
            return Property::query()
                ->where('author_id', auth('account')->id())
                ->where('author_type', Account::class);
        }

        return Property::query()->where('member_id', auth('member')->id());
    }

    protected function authorizeAccess(string $role, Property $property): void
    {
        switch ($role) {
            case 'admin':
                abort_unless(auth()->user() && auth()->user()->hasPermission('property.edit'), 403);

                return;
            case 'agent':
                abort_unless(
                    $property->author_id == auth('account')->id() && $property->author_type === Account::class,
                    403
                );

                return;
            case 'member':
                abort_unless($property->member_id == auth('member')->id(), 403);

                return;
            default:
                abort_unless((int) session('wizard_draft_id') === (int) $property->id, 403);
        }
    }

    protected function wizardContext(string $role): array
    {
        return [
            'role' => $role,
            'can' => [
                'setFeatured' => $role === 'admin',
                'setModerationStatus' => $role === 'admin',
                'showVerification' => in_array($role, ['agent', 'admin'], true),
            ],
        ];
    }

    protected function defaultStepFor(Property $property): int
    {
        if ($property->isSubmitted()) {
            return 1;
        }

        return min(4, ((int) $property->wizard_step) + 1) ?: 1;
    }
}
