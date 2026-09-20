<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Location\Models\Country;
use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Http\Requests\PropertyWizardAgentStepRequest;
use Botble\RealEstate\Http\Requests\PropertyWizardBasicsStepRequest;
use Botble\RealEstate\Http\Requests\PropertyWizardLocationStepRequest;
use Botble\RealEstate\Http\Requests\PropertyWizardMediaStepRequest;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Category;
use Botble\RealEstate\Models\Comment;
use Botble\RealEstate\Models\CategoryDocument;
use Botble\RealEstate\Models\Currency;
use Botble\RealEstate\Models\Facility;
use Botble\RealEstate\Models\Feature;
use Botble\RealEstate\Models\Member;
use Botble\RealEstate\Models\Project;
use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Services\PropertyContractService;
use Botble\RealEstate\Services\PropertySubmissionService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use SeoHelper;
use Theme;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use EmailHandler;

class PropertyWizardController extends Controller
{
    /**
     * @var PropertySubmissionService
     */
    protected $service;

    /**
     * @var PropertyContractService
     */
    protected $contractService;

    public function __construct(PropertySubmissionService $service, PropertyContractService $contractService)
    {
        $this->service = $service;
        $this->contractService = $contractService;
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
            'isLocked' => $this->isLocked($property),
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
            'signContractUrl' => $this->isFullyVerified($property)
                ? route($this->routeName($role, 'sign-contract'), ['property' => $property->id])
                : null,
            'listingPaymentUrl' => $this->isContractFullySigned($property)
                ? route($this->routeName($role, 'listing-payment'), ['property' => $property->id])
                : null,
            'adListingUrl' => $this->isPaymentComplete($property)
                ? route($this->routeName($role, 'ad-listing'), ['property' => $property->id])
                : null,
            'uploadUrl' => $this->uploadUrlFor($role),
            'authenticateUrl' => $role === 'guest' ? route('general-property-wizard.authenticate') : null,
            'categories' => Category::where('status', BaseStatusEnum::PUBLISHED)->orderBy('name')->get(['id', 'name', 'parent_id']),
            // Project has no draft/published concept of its own (its 'status'
            // column is a sale/rent lifecycle state, not visibility), so
            // every project is a valid choice here.
            'projects' => Project::orderBy('name')->get(['id', 'name']),
            'currencies' => Currency::orderBy('order')->get(['id', 'title', 'symbol', 'is_default']),
            'countries' => Country::where('status', BaseStatusEnum::PUBLISHED)->orderBy('name')->get(['id', 'name']),
            'features' => Feature::where('status', BaseStatusEnum::PUBLISHED)->orderBy('name')->get(['id', 'name']),
            'facilities' => Facility::where('status', BaseStatusEnum::PUBLISHED)->orderBy('name')->get([
                'id',
                'name',
                'google_place_type',
                'google_place_keyword',
                'google_place_radius',
            ]),
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

    /**
     * Where the "back to your listings" link on the final Ad Listing screen
     * should go for each role.
     */
    protected function dashboardUrlFor(string $role): string
    {
        switch ($role) {
            case 'admin':
                return route('property.index');
            case 'agent':
                return route('public.account.properties.index');
            default:
                return route('public.member.properties.index');
        }
    }

    public function saveBasics(PropertyWizardBasicsStepRequest $request, Property $property)
    {
        $role = $this->currentRole($request);
        $this->authorizeAccess($role, $property);

        abort_if($this->isLocked($property), 403);

        // Whether this property already has a slugs row - checked before
        // saving name changes below, since CreatedContentEvent vs
        // UpdatedContentEvent must reflect the state *before* this save.
        $hadSlug = (bool) $property->slugable;

        $property = $this->service->saveBasics($property, $request->validated());

        // The wizard bypasses the old admin CRUD controller entirely, which
        // is what used to fire these - without it, a property never gets a
        // row in the polymorphic slugs table, so $property->url silently
        // falls back to the bare site root everywhere it's linked from.
        // Basics is the first (and only) step that sets a real name.
        // Deliberately NOT always using UpdatedContentEvent: its "no
        // existing row" fallback stores the raw name unslugified (a quirk
        // in Botble\Slug\Listeners\UpdatedContentListener - only the
        // "existing row" branch runs the value through SlugService::create()).
        // CreatedContentListener always slugifies, so it's used for the
        // property's first-ever slug; every later rename goes through
        // UpdatedContentEvent, which by then finds the existing row.
        if ($hadSlug) {
            event(new UpdatedContentEvent(PROPERTY_MODULE_SCREEN_NAME, $request, $property));
        } else {
            event(new CreatedContentEvent(PROPERTY_MODULE_SCREEN_NAME, $request, $property));
        }

        return $this->stepSavedResponse($role, $property, 2);
    }

    public function saveLocation(PropertyWizardLocationStepRequest $request, Property $property)
    {
        $role = $this->currentRole($request);
        $this->authorizeAccess($role, $property);

        abort_if($this->isLocked($property), 403);

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

        abort_if($this->isLocked($property), 403);

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

        abort_if($this->isLocked($property), 403);

        if ($role === 'guest' && ! $property->member_id) {
            return response()->json([
                'success' => false,
                'require_auth' => true,
            ], 422);
        }

        $wasSubmitted = $property->isSubmitted();
        $property = $this->service->finalize($property);

        if (! $wasSubmitted) {
            $this->notifyPropertySubmitted($property);
        } else {
            // Already submitted before (e.g. revisiting the wizard on a
            // property that's already past Choose Agent) - this is an edit,
            // not a first-time submission.
            $this->notifyPropertyUpdated($property);
        }

        return response()->json([
            'success' => true,
            'redirect_url' => route(
                $this->routeName($role, $this->hasAssignedAgent($property) ? 'ad-verification' : 'choose-agent'),
                ['property' => $property->id]
            ),
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
                'verification_token' => Str::random(64),
                'email_verified' => false,
            ]);

            // Attach ownership now (safe - this only tags the draft, it
            // doesn't submit it) rather than waiting for verification: the
            // member may click the email link on a different device, or
            // long after this guest session has expired, so verifyMemberEmail()
            // needs to be able to find this draft from the member alone.
            $this->service->claimGuestDraftForMember($property, $member);
            $this->sendMemberVerificationEmail($member);

            session()->forget('wizard_draft_id');

            return response()->json([
                'success' => true,
                'require_email_verification' => true,
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
        $wasSubmitted = $property->isSubmitted();
        $property = $this->service->finalize($property);

        if (! $wasSubmitted) {
            $this->notifyPropertySubmitted($property);
        }

        Auth::guard('member')->login($member);

        session()->forget('wizard_draft_id');

        return response()->json([
            'success' => true,
            'redirect_url' => route('public.member.properties.wizard.choose-agent', ['property' => $property->id]),
        ]);
    }

    /**
     * Same verification email/token/link as the regular member-signup form
     * (GeneralPropertyController::createMember()), so a wizard-created
     * account goes through the exact same security model as any other.
     */
    protected function sendMemberVerificationEmail(Member $member): void
    {
        $link = url('/member/verify/' . $member->verification_token);

        Mail::send(
            'plugins/real-estate::account.emails.verify-email',
            ['link' => $link],
            function ($message) use ($member) {
                $message->to($member->email)->subject('Verify Your Email');
            }
        );
    }

    /**
     * Handles /member/verify/{token} for both the regular member-signup
     * form and the wizard's guest "create a new account" path. If the
     * member owns a draft property (only true for the wizard path -
     * claimGuestDraftForMember() tags it at account-creation time, before
     * verification), finalize it and drop them back into the wizard;
     * otherwise fall back to the original "verified, please log in" flow.
     */
    public function verifyMemberEmail(string $token)
    {
        $member = Member::where('verification_token', $token)->first();

        if (! $member) {
            return redirect()->route('member.login')->with('error_msg', __('Invalid verification link.'));
        }

        $member->email_verified = true;
        $member->verification_token = null;
        $member->save();

        $draft = Property::where('member_id', $member->id)
            ->where('submission_status', 'draft')
            ->where('is_deleted', 0)
            ->latest('id')
            ->first();

        if (! $draft) {
            return redirect()->route('member.login')->with('success_msg', __('Email verified successfully. You may now login.'));
        }

        Auth::guard('member')->login($member);

        $draft = $this->service->finalize($draft);
        $this->notifyPropertySubmitted($draft);

        return redirect()
            ->route('public.member.properties.wizard.choose-agent', ['property' => $draft->id])
            ->with('success_msg', __('Your email has been verified. You can continue adding your property.'));
    }

    public function chooseAgentPlaceholder(Request $request, Property $property)
    {
        $role = $this->currentRole($request);
        $this->authorizeAccess($role, $property);

        // An agent submitting their own listing is already its assigned
        // agent - there's nobody for them to choose, so this step doesn't
        // apply to them even if they land here via a stale link.
        if ($role === 'agent') {
            return redirect()->route($this->routeName($role, 'ad-verification'), ['property' => $property->id]);
        }

        return view('plugins/real-estate::wizard.choose-agent-placeholder', [
            'role' => $role,
            'property' => $property,
            'locked' => $this->isLocked($property),
            'chooseAgentUrl' => route($this->routeName($role, 'choose-agent'), ['property' => $property->id]),
            'saveAgentUrl' => route($this->routeName($role, 'save-agent'), ['property' => $property->id]),
            'showBaseUrl' => route($this->routeName($role, 'show'), ['property' => $property->id]),
            'adVerificationUrl' => $this->hasAssignedAgent($property)
                ? route($this->routeName($role, 'ad-verification'), ['property' => $property->id])
                : null,
            'nearbyAgents' => $this->nearbyAgentsFor($property, $role),
        ]);
    }

    public function saveAgent(PropertyWizardAgentStepRequest $request, Property $property)
    {
        $role = $this->currentRole($request);
        $this->authorizeAccess($role, $property);

        if ($this->isLocked($property)) {
            return response()->json([
                'success' => false,
                'message' => __('This listing has already been approved and can no longer be edited.'),
            ], 403);
        }

        $newAgentId = (int) $request->validated()['agent_id'];
        $agentChanged = ! $this->hasAssignedAgent($property) || (int) $property->author_id !== $newAgentId;

        $property->author_id = $newAgentId;
        $property->author_type = Account::class;
        $property->save();

        // Revisiting this step and re-saving the same agent (e.g. just
        // passing back through the wizard on a property that's already past
        // this point) shouldn't re-notify anyone - only an actual change of
        // agent should.
        if ($agentChanged) {
            $this->notifyAgentAssigned($property);
        }

        return response()->json([
            'success' => true,
            'next_url' => route($this->routeName($role, 'ad-verification'), ['property' => $property->id]),
        ]);
    }

    /**
     * Ad Verification (global step 3) - unlocked once an agent is assigned.
     * The agent verifies the listing first, then the admin verifies it too;
     * nobody can move on to the next step until both have signed off.
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
            'locked' => $this->isLocked($property),
            'verified' => (bool) $property->verified,
            'verifiedByAdmin' => (bool) $property->verified_by_admin,
            'comments' => $property->comments()->orderBy('created_at')->get(),
            'categoryDocuments' => $property->category_id
                ? CategoryDocument::with('document')->where('category_id', $property->category_id)->orderBy('id')->get()
                : collect(),
            'chooseAgentUrl' => route($this->routeName($role, 'choose-agent'), ['property' => $property->id]),
            'showBaseUrl' => route($this->routeName($role, 'show'), ['property' => $property->id]),
            'verifyAgentUrl' => $role === 'agent'
                ? route($this->routeName($role, 'ad-verification.verify-agent'), ['property' => $property->id])
                : null,
            'verifyAdminUrl' => $role === 'admin'
                ? route($this->routeName($role, 'ad-verification.verify-admin'), ['property' => $property->id])
                : null,
            'commentStoreUrl' => in_array($role, ['agent', 'member'], true)
                ? route($this->routeName($role, 'ad-verification.comment'), ['property' => $property->id])
                : null,
            'signContractUrl' => $this->isFullyVerified($property)
                ? route($this->routeName($role, 'sign-contract'), ['property' => $property->id])
                : null,
        ]);
    }

    /**
     * Sign Contract (global step 4) - unlocked once both the agent and admin
     * have verified the property. Only the member (seller) and agent need to
     * sign now - each one's signature is pulled automatically from their own
     * account (captured once on their settings page, not per-property). This
     * page never auto-redirects a party away for a missing signature - it
     * always renders, and whoever is missing their own signature sees a
     * link to their settings page (with a return_to flag) right on their
     * own status row, which bounces them back here once saved.
     *
     * The contract document itself is always visible - a live preview
     * renders from whatever data currently exists (blank signature boxes
     * for whoever hasn't signed yet), regardless of whether both parties
     * are done, and regardless of whether it's been finalized before. Once
     * finalized (contract_finalized_at set), "Save & Continue" is available
     * again on any later pass through this step and simply regenerates an
     * up-to-date copy on disk - without re-emailing, since that only ever
     * goes out once - see finalizeContract().
     */
    public function signContractPlaceholder(Request $request, Property $property)
    {
        $role = $this->currentRole($request);
        $this->authorizeAccess($role, $property);

        if (! $this->isFullyVerified($property)) {
            return redirect()->route($this->routeName($role, 'ad-verification'), ['property' => $property->id]);
        }

        $requiresMember = $this->contractService->requiresMember($property);
        $memberSigned = $this->contractService->memberHasSignature($property);
        $agentSigned = $this->contractService->agentHasSignature($property);
        $readyToSign = $this->contractService->isReadyToGenerate($property);
        $alreadyFinalized = (bool) $property->contract_finalized_at;

        return view('plugins/real-estate::wizard.sign-contract-placeholder', [
            'role' => $role,
            'property' => $property,
            'requiresMember' => $requiresMember,
            'memberSigned' => $memberSigned,
            'agentSigned' => $agentSigned,
            'readyToSign' => $readyToSign,
            'alreadyFinalized' => $alreadyFinalized,
            'memberSignatureUrl' => route('member.settings', ['return_to' => 'wizard-contract', 'property' => $property->id]),
            'agentSignatureUrl' => route('public.account.settings', ['return_to' => 'wizard-contract', 'property' => $property->id]),
            'downloadUrl' => route($this->routeName($role, 'sign-contract.download'), [
                'property' => $property->id,
                'copy' => $role === 'member' ? 'member' : 'agent',
            ]),
            'finalizeUrl' => route($this->routeName($role, 'sign-contract.finalize'), ['property' => $property->id]),
            'chooseAgentUrl' => route($this->routeName($role, 'choose-agent'), ['property' => $property->id]),
            'adVerificationUrl' => route($this->routeName($role, 'ad-verification'), ['property' => $property->id]),
            'showBaseUrl' => route($this->routeName($role, 'show'), ['property' => $property->id]),
            'listingPaymentUrl' => $alreadyFinalized
                ? route($this->routeName($role, 'listing-payment'), ['property' => $property->id])
                : null,
        ]);
    }

    /**
     * The "Save & Continue" action: finalizes the contract - renders it
     * fresh from whatever data currently exists and writes both permanent
     * copies to disk (each under its own new timestamped filename - see
     * PropertyContractService::generate()) - then moves on to Listing
     * Payment. Only reachable once both required signatures actually exist.
     * Every submission regenerates the PDFs: nothing about the files is
     * write-once - a property can cycle back through earlier steps (e.g.
     * the price changes), return here, and the next "Save & Continue"
     * produces an up-to-date contract reflecting that.
     *
     * The notification email is different: it goes out exactly once, on
     * the very first submission (contract_finalized_at not yet set) -
     * later regenerations are silent so re-visiting this step to pick up a
     * data change doesn't re-spam both parties' inboxes every click.
     */
    public function finalizeContract(Request $request, Property $property)
    {
        $role = $this->currentRole($request);
        $this->authorizeAccess($role, $property);

        abort_if($this->isLocked($property), 403);
        abort_unless($this->contractService->isReadyToGenerate($property), 403);

        $isFirstFinalization = ! $property->contract_finalized_at;

        $plaintext = $this->contractService->generate($property);

        if ($isFirstFinalization) {
            $this->notifyContractFinalized($property, $plaintext);
            $property->contract_finalized_at = now();
            $property->save();
        }

        return redirect()->route($this->routeName($role, 'listing-payment'), ['property' => $property->id]);
    }

    /**
     * Streams one party's copy of the contract PDF. Always rendered fresh
     * from whatever property/member/agent data currently exists - never
     * from the encrypted, previously-emailed copy on disk - so the page
     * never shows stale terms after e.g. the price is edited on an earlier
     * step. The encrypted copies written by finalizeContract() exist purely
     * as an audit trail of what was actually emailed at each point in time,
     * not as the source for this preview. Reuses the same ownership gate
     * every other wizard action uses, so a party can never fetch another
     * property's contract by guessing an id.
     */
    public function downloadContract(Request $request, Property $property, string $copy)
    {
        $role = $this->currentRole($request);
        $this->authorizeAccess($role, $property);

        abort_unless(in_array($copy, ['member', 'agent'], true), 404);

        $pdfContent = $this->contractService->renderPdf($property);

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"signed-contract-{$copy}-copy.pdf\"",
        ]);
    }

    /**
     * Listing Payment (global step 5) - unlocked once all three parties have
     * signed the contract. The member spends 1 credit to publish; agent and
     * admin are just kept informed.
     */
    public function listingPaymentPlaceholder(Request $request, Property $property)
    {
        $role = $this->currentRole($request);
        $this->authorizeAccess($role, $property);

        if (! $this->isContractFullySigned($property)) {
            return redirect()->route($this->routeName($role, 'sign-contract'), ['property' => $property->id]);
        }

        [$payer, $payerRole] = $this->payerFor($property);
        $isPaid = $this->isPaymentComplete($property);

        return view('plugins/real-estate::wizard.listing-payment-placeholder', [
            'role' => $role,
            'property' => $property,
            'isPaid' => $isPaid,
            'payerRole' => $payerRole,
            'payerName' => $payer
                ? ($payerRole === 'member' ? $payer->full_name : $payer->getFullName())
                : __('the member'),
            'hasCredits' => (bool) $payer && $payer->credits >= 1,
            'chooseAgentUrl' => route($this->routeName($role, 'choose-agent'), ['property' => $property->id]),
            'adVerificationUrl' => route($this->routeName($role, 'ad-verification'), ['property' => $property->id]),
            'signContractUrl' => route($this->routeName($role, 'sign-contract'), ['property' => $property->id]),
            'showBaseUrl' => route($this->routeName($role, 'show'), ['property' => $property->id]),
            'confirmPaymentUrl' => $role === $payerRole
                ? route($this->routeName($role, 'listing-payment.confirm'), ['property' => $property->id])
                : null,
            'buyCreditsUrl' => $payerRole === 'agent'
                ? route('public.account.packages', [
                    'redirect_to' => route($this->routeName($role, 'listing-payment'), ['property' => $property->id]),
                ])
                : route('public.member.packages', [
                    'redirect_to' => route($this->routeName($role, 'listing-payment'), ['property' => $property->id]),
                ]),
            'adListingUrl' => $isPaid
                ? route($this->routeName($role, 'ad-listing'), ['property' => $property->id])
                : null,
        ]);
    }

    /**
     * The payer confirms payment: 1 credit is deducted and the property is
     * approved for listing. Everyone else just sees the resulting state on
     * their next visit to this page - no action of their own here.
     */
    public function confirmListingPayment(Request $request, Property $property)
    {
        $role = $this->currentRole($request);
        $this->authorizeAccess($role, $property);

        [$payer, $payerRole] = $this->payerFor($property);

        abort_unless($payer && $role === $payerRole, 403);

        if (! $this->isPaymentComplete($property)) {
            abort_unless($payer->credits >= 1, 422);

            $payer->credits--;
            $payer->save();

            $property->moderation_status = ModerationStatusEnum::APPROVED;
            $property->save();

            $this->notifyListingPaymentConfirmed($property, $payer);
        }

        return redirect()->route($this->routeName($role, 'listing-payment'), ['property' => $property->id]);
    }

    /**
     * Who owes the 1-credit listing payment: the member for a member-owned
     * listing, or the assigned agent themselves for a listing with no
     * member (e.g. an agent's own submission). [null, null] if neither
     * applies yet (shouldn't normally be reachable - Sign Contract requires
     * an assigned agent at minimum before this step ever unlocks).
     *
     * @return array{0: Member|Account|null, 1: string|null}
     */
    protected function payerFor(Property $property): array
    {
        if ($property->member) {
            return [$property->member, 'member'];
        }

        if ($this->hasAssignedAgent($property)) {
            return [Account::find($property->author_id), 'agent'];
        }

        return [null, null];
    }

    /**
     * Ad Listing (global step 6) - the final "you're live" screen, unlocked
     * once the listing payment has gone through.
     */
    public function adListing(Request $request, Property $property)
    {
        $role = $this->currentRole($request);
        $this->authorizeAccess($role, $property);

        if (! $this->isPaymentComplete($property)) {
            return redirect()->route($this->routeName($role, 'listing-payment'), ['property' => $property->id]);
        }

        return view('plugins/real-estate::wizard.ad-listing-placeholder', [
            'role' => $role,
            'property' => $property,
            'dashboardUrl' => $this->dashboardUrlFor($role),
        ]);
    }

    /**
     * The agent's sign-off on the Ad Verification step. Only flips the flag
     * and notifies admin/member - moving on to the next step still waits for
     * the admin's own sign-off below.
     */
    public function verifyByAgent(Request $request, Property $property)
    {
        $role = $this->currentRole($request);
        $this->authorizeAccess($role, $property);

        abort_if($this->isLocked($property), 403);

        $account = auth('account')->user();

        $property->verified = true;
        $property->save();

        $this->notifyVerifiedByAgent($property, $account);

        return redirect()->route($this->routeName($role, 'ad-verification'), ['property' => $property->id]);
    }

    /**
     * The admin's sign-off, blocked server-side (not just in the UI) until
     * the agent has already verified.
     */
    public function verifyByAdmin(Request $request, Property $property)
    {
        $role = $this->currentRole($request);
        $this->authorizeAccess($role, $property);

        abort_if($this->isLocked($property), 403);
        abort_unless($property->verified, 403);

        $property->verified_by_admin = true;
        $property->save();

        $this->notifyVerifiedByAdmin($property);

        return redirect()->route($this->routeName($role, 'ad-verification'), ['property' => $property->id]);
    }

    /**
     * A message on the Ad Verification step's comment thread between the
     * agent and the member - admin can read it but has no route to post to.
     */
    public function storeComment(Request $request, Property $property)
    {
        $role = $this->currentRole($request);
        $this->authorizeAccess($role, $property);

        abort_if($this->isLocked($property), 403);
        abort_unless(in_array($role, ['agent', 'member'], true), 403);

        $data = $request->validate([
            'comment' => ['required', 'string', 'max:2000'],
        ]);

        $comment = new Comment(['comment' => $data['comment']]);

        if ($role === 'agent') {
            $comment->user()->associate(auth('account')->user());
        } else {
            $comment->member_id = auth('member')->id();
        }

        $property->comments()->save($comment);

        return redirect()->route($this->routeName($role, 'ad-verification'), ['property' => $property->id]);
    }

    /**
     * Member submitted the wizard - notify the member (confirmation) and
     * admin (heads up). Only fires for real member submissions, not an
     * admin/agent submitting a listing of their own with no member attached.
     */
    protected function notifyPropertySubmitted(Property $property): void
    {
        $member = $property->member;

        if (! $member) {
            return;
        }

        $this->sendWizardEmail('property_submitted_member', $member->email, [
            'recipient_name' => $member->full_name,
            'member_name' => $member->full_name,
            'property_title' => $property->name,
            'property_url' => route('public.member.properties.edit', ['property' => $property->id]),
        ]);

        $this->sendWizardEmail('property_submitted_admin', setting('admin_email'), [
            'recipient_name' => __('Admin'),
            'member_name' => $member->full_name,
            'property_title' => $property->name,
            'property_url' => route('property.edit', ['property' => $property->id]),
        ]);
    }

    /**
     * The wizard was submitted again on a property that was already
     * submitted before - an edit, not a first-time submission. Only member
     * and agent are told (not admin, per spec).
     */
    protected function notifyPropertyUpdated(Property $property): void
    {
        $member = $property->member;
        $agent = $property->author_type === Account::class ? Account::find($property->author_id) : null;

        if ($member) {
            $this->sendWizardEmail('property_updated_member', $member->email, [
                'recipient_name' => $member->full_name,
                'property_title' => $property->name,
                'property_url' => route('public.member.properties.edit', ['property' => $property->id]),
            ]);
        }

        if ($agent) {
            $this->sendWizardEmail('property_updated_agent', $agent->email, [
                'recipient_name' => $agent->getFullName(),
                'property_title' => $property->name,
                'property_url' => route('public.account.properties.edit', ['property' => $property->id]),
            ]);
        }
    }

    /**
     * An agent was assigned (by whoever - member or admin) - notify all
     * three parties: the agent, the member (if any), and admin.
     */
    protected function notifyAgentAssigned(Property $property): void
    {
        $agent = Account::find($property->author_id);
        $member = $property->member;

        if ($agent) {
            $this->sendWizardEmail('agent_assigned_agent', $agent->email, [
                'recipient_name' => $agent->getFullName(),
                'agent_name' => $agent->getFullName(),
                'member_name' => $member ? $member->full_name : __('N/A'),
                'property_title' => $property->name,
                'property_url' => route('public.account.properties.edit', ['property' => $property->id]),
            ]);
        }

        if ($member) {
            $this->sendWizardEmail('agent_assigned_member', $member->email, [
                'recipient_name' => $member->full_name,
                'agent_name' => $agent ? $agent->getFullName() : __('N/A'),
                'member_name' => $member->full_name,
                'property_title' => $property->name,
                'property_url' => route('public.member.properties.edit', ['property' => $property->id]),
            ]);
        }

        $this->sendWizardEmail('agent_assigned_admin', setting('admin_email'), [
            'recipient_name' => __('Admin'),
            'agent_name' => $agent ? $agent->getFullName() : __('N/A'),
            'member_name' => $member ? $member->full_name : __('N/A'),
            'property_title' => $property->name,
            'property_url' => route('property.edit', ['property' => $property->id]),
        ]);
    }

    /**
     * Agent verified the listing - notify the member and admin. The agent
     * themselves doesn't need telling, they just did it.
     */
    protected function notifyVerifiedByAgent(Property $property, Account $agent): void
    {
        $member = $property->member;

        if ($member) {
            $this->sendWizardEmail('property_verified_by_agent_member', $member->email, [
                'recipient_name' => $member->full_name,
                'agent_name' => $agent->getFullName(),
                'property_title' => $property->name,
                'property_url' => route('public.member.properties.edit', ['property' => $property->id]),
            ]);
        }

        $this->sendWizardEmail('property_verified_by_agent_admin', setting('admin_email'), [
            'recipient_name' => __('Admin'),
            'agent_name' => $agent->getFullName(),
            'property_title' => $property->name,
            'property_url' => route('property.edit', ['property' => $property->id]),
        ]);
    }

    /**
     * Admin gave the final sign-off - notify the member and the agent.
     * Admin doesn't need telling, they just did it.
     */
    protected function notifyVerifiedByAdmin(Property $property): void
    {
        $member = $property->member;
        $agent = $property->author_type === Account::class ? Account::find($property->author_id) : null;

        if ($member) {
            $this->sendWizardEmail('property_verified_by_admin_member', $member->email, [
                'recipient_name' => $member->full_name,
                'property_title' => $property->name,
                'property_url' => route('public.member.properties.edit', ['property' => $property->id]),
            ]);
        }

        if ($agent) {
            $this->sendWizardEmail('property_verified_by_admin_agent', $agent->email, [
                'recipient_name' => $agent->getFullName(),
                'property_title' => $property->name,
                'property_url' => route('public.account.properties.edit', ['property' => $property->id]),
            ]);
        }
    }

    /**
     * The payer's payment went through - only the payer is emailed here (per
     * spec); agent/admin just see the updated state on the page. The payer
     * is the member for a member-owned listing, or the assigned agent
     * themselves for a listing with no member (e.g. an agent's own
     * submission).
     *
     * @param Member|Account $payer
     */
    protected function notifyListingPaymentConfirmed(Property $property, $payer): void
    {
        if ($payer instanceof Member) {
            $this->sendWizardEmail('listing_payment_confirmed_member', $payer->email, [
                'recipient_name' => $payer->full_name,
                'property_title' => $property->name,
                'property_url' => route('public.member.properties.edit', ['property' => $property->id]),
            ]);

            return;
        }

        $this->sendWizardEmail('listing_payment_confirmed_agent', $payer->email, [
            'recipient_name' => $payer->getFullName(),
            'property_title' => $property->name,
            'property_url' => route('public.account.properties.edit', ['property' => $property->id]),
        ]);
    }

    /**
     * The contract was just finalized (both required signatures were
     * present, "Save & Continue" was clicked) - each party gets their own
     * named copy attached. A property with no member (an agent's own
     * listing) simply has no member email to send.
     *
     * $plaintext is the same in-memory bytes PropertyContractService::
     * generate() just encrypted for storage - attached here directly from
     * memory (never re-read/decrypted from disk) so the plaintext PDF only
     * ever exists transiently in this one request, never persisted.
     */
    protected function notifyContractFinalized(Property $property, string $plaintext): void
    {
        $member = $property->member;
        $agent = $property->author_type === Account::class ? Account::find($property->author_id) : null;

        if ($member) {
            $this->sendWizardEmail('contract_finalized_member', $member->email, [
                'recipient_name' => $member->full_name,
                'property_title' => $property->name,
                'property_url' => route('public.member.properties.wizard.sign-contract', ['property' => $property->id]),
            ], [
                ['data' => $plaintext, 'name' => 'signed-contract-member-copy.pdf'],
            ]);
        }

        if ($agent) {
            $this->sendWizardEmail('contract_finalized_agent', $agent->email, [
                'recipient_name' => $agent->getFullName(),
                'property_title' => $property->name,
                'property_url' => route('public.account.properties.wizard.sign-contract', ['property' => $property->id]),
            ], [
                ['data' => $plaintext, 'name' => 'signed-contract-agent-copy.pdf'],
            ]);
        }
    }

    /**
     * Every wizard notification email funnels through here - registered
     * under module 'real-estate' (see RealEstateServiceProvider::boot()) so
     * each template's body and on/off toggle are editable from Admin >
     * Settings > Email, same as every other template in this plugin.
     *
     * $attachData items are ['data' => raw bytes, 'name' => filename] pairs
     * attached directly from memory (EmailAbstract::build()'s attach_data
     * support) - not file paths - since the only on-disk copy of a
     * finalized contract is encrypted, not a real PDF.
     */
    protected function sendWizardEmail(string $template, ?string $email, array $values, array $attachData = []): void
    {
        if (! $email) {
            return;
        }

        // The subject is passed explicitly (from this same config, so it
        // stays in one place) rather than left for EmailHandler to resolve
        // itself: get_setting_email_subject() only checks config keyed by
        // the literal filename "email.php" for a module, which these
        // wizard-only templates deliberately don't live in (keeps them from
        // cluttering the unrelated consult-form email settings group).
        // Passed subjects still get their own {{ variable }} substitution,
        // same as the body - see EmailHandler::send()'s $title handling.
        $subject = config("plugins.real-estate.wizard-email.templates.$template.subject");

        $args = $attachData ? ['attach_data' => $attachData] : [];

        EmailHandler::setModule('real-estate')
            ->addVariables(config('plugins.real-estate.wizard-email.variables', []))
            ->setVariableValues($values)
            ->sendUsingTemplate($template, $email, $args, false, 'plugins', $subject);
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
     * Whether both the agent and admin have signed off on the Ad
     * Verification step - i.e. whether Sign Contract is unlocked.
     */
    protected function isFullyVerified(Property $property): bool
    {
        return (bool) $property->verified && (bool) $property->verified_by_admin;
    }

    /**
     * Once a listing is approved, it's done - nobody (member, agent, or
     * admin) can change anything about it or its wizard state any further,
     * and no wizard action should ever notify anyone again. Every mutating
     * wizard action checks this before doing anything; view-only actions
     * don't - people can still browse through the wizard's pages to see
     * what happened, they just can't act on any of them anymore.
     */
    protected function isLocked(Property $property): bool
    {
        return (string) $property->moderation_status === ModerationStatusEnum::APPROVED;
    }

    /**
     * Whether the contract has actually been finalized - both PDF copies
     * written and both parties emailed - i.e. whether Listing Payment is
     * unlocked. contract_finalized_at is the single source of truth here,
     * not "do the PDF files exist" (files can exist without an email ever
     * having been sent - see PropertyContractService/finalizeContract()).
     */
    protected function isContractFullySigned(Property $property): bool
    {
        return (bool) $property->contract_finalized_at;
    }

    /**
     * Whether the member's listing payment has gone through - i.e. whether
     * Ad Listing is unlocked. moderation_status is set to APPROVED
     * exclusively by confirmListingPayment(), so this doubles as "has the
     * member paid" without needing a separate column.
     */
    protected function isPaymentComplete(Property $property): bool
    {
        return (string) $property->moderation_status === ModerationStatusEnum::APPROVED;
    }

    /**
     * Agents eligible for this property: whichever agents' drawn coverage
     * area contains its location, plus whichever agent is already assigned
     * (kept visible even if they no longer match, e.g. after the property's
     * location was edited). Admins aren't limited to location coverage -
     * they can assign any agent in the system.
     */
    protected function nearbyAgentsFor(Property $property, string $role = 'member')
    {
        if ($role === 'admin') {
            return Account::query()->orderBy('first_name')->orderBy('last_name')->get();
        }

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
            $existingDraft = $this->ownedQuery($role)
                ->where('submission_status', 'draft')
                ->where('is_deleted', 0)
                ->latest('id')
                ->first();

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
