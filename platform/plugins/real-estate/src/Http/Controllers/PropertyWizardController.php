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
use Botble\RealEstate\Models\Comment;
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
use EmailHandler;

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
            'signContractUrl' => $this->isFullyVerified($property)
                ? route($this->routeName($role, 'sign-contract'), ['property' => $property->id])
                : null,
            'listingPaymentUrl' => $this->isContractFullySigned($property)
                ? route($this->routeName($role, 'listing-payment'), ['property' => $property->id])
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

        $wasSubmitted = $property->isSubmitted();
        $property = $this->service->finalize($property);

        if (! $wasSubmitted) {
            $this->notifyPropertySubmitted($property);
        }

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

        $this->notifyAgentAssigned($property);

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
            'verified' => (bool) $property->verified,
            'verifiedByAdmin' => (bool) $property->verified_by_admin,
            'comments' => $property->comments()->orderBy('created_at')->get(),
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
     * have verified the property. All three parties (member, agent, admin)
     * must sign, in any order, before Listing Payment unlocks.
     */
    public function signContractPlaceholder(Request $request, Property $property)
    {
        $role = $this->currentRole($request);
        $this->authorizeAccess($role, $property);

        if (! $this->isFullyVerified($property)) {
            return redirect()->route($this->routeName($role, 'ad-verification'), ['property' => $property->id]);
        }

        $signedByMember = (bool) $property->contract_signed_by_member;
        $signedByAgent = (bool) $property->contract_signed_by_agent;
        $signedByAdmin = (bool) $property->contract_signed_by_admin;
        $allSigned = $signedByMember && $signedByAgent && $signedByAdmin;

        $signedByRoleMap = ['member' => $signedByMember, 'agent' => $signedByAgent, 'admin' => $signedByAdmin];

        return view('plugins/real-estate::wizard.sign-contract-placeholder', [
            'role' => $role,
            'property' => $property,
            'signedByMember' => $signedByMember,
            'signedByAgent' => $signedByAgent,
            'signedByAdmin' => $signedByAdmin,
            'allSigned' => $allSigned,
            'signedByRole' => $signedByRoleMap[$role] ?? false,
            'chooseAgentUrl' => route($this->routeName($role, 'choose-agent'), ['property' => $property->id]),
            'adVerificationUrl' => route($this->routeName($role, 'ad-verification'), ['property' => $property->id]),
            'showBaseUrl' => route($this->routeName($role, 'show'), ['property' => $property->id]),
            'signUrl' => route($this->routeName($role, 'sign-contract.sign'), ['property' => $property->id]),
            'listingPaymentUrl' => $allSigned
                ? route($this->routeName($role, 'listing-payment'), ['property' => $property->id])
                : null,
        ]);
    }

    /**
     * One party (whichever role the request belongs to) signs the contract.
     * Order doesn't matter - each party's flag is independent, and Listing
     * Payment only unlocks once all three are set.
     */
    public function signContract(Request $request, Property $property)
    {
        $role = $this->currentRole($request);
        $this->authorizeAccess($role, $property);

        $column = [
            'member' => 'contract_signed_by_member',
            'agent' => 'contract_signed_by_agent',
            'admin' => 'contract_signed_by_admin',
        ][$role] ?? null;

        abort_unless($column, 403);

        if (! $property->{$column}) {
            $property->{$column} = true;
            $property->save();

            $this->notifyContractSigned($property, $role);
        }

        return redirect()->route($this->routeName($role, 'sign-contract'), ['property' => $property->id]);
    }

    /**
     * Listing Payment (global step 5) - unlocked once all three parties have
     * signed the contract. Just a placeholder screen for now.
     */
    public function listingPaymentPlaceholder(Request $request, Property $property)
    {
        $role = $this->currentRole($request);
        $this->authorizeAccess($role, $property);

        if (! $this->isContractFullySigned($property)) {
            return redirect()->route($this->routeName($role, 'sign-contract'), ['property' => $property->id]);
        }

        return view('plugins/real-estate::wizard.listing-payment-placeholder', [
            'role' => $role,
            'property' => $property,
            'chooseAgentUrl' => route($this->routeName($role, 'choose-agent'), ['property' => $property->id]),
            'signContractUrl' => route($this->routeName($role, 'sign-contract'), ['property' => $property->id]),
            'showBaseUrl' => route($this->routeName($role, 'show'), ['property' => $property->id]),
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
     * One party signed the Sign Contract step - notify whichever of the
     * other two parties actually exist, using a template tailored to the
     * (signer, recipient) pair (e.g. contract_signed_by_agent_member).
     */
    protected function notifyContractSigned(Property $property, string $signerRole): void
    {
        $member = $property->member;
        $agent = $property->author_type === Account::class ? Account::find($property->author_id) : null;

        $recipients = [
            'member' => $member ? [
                'name' => $member->full_name,
                'email' => $member->email,
                'url' => route('public.member.properties.wizard.sign-contract', ['property' => $property->id]),
            ] : null,
            'agent' => $agent ? [
                'name' => $agent->getFullName(),
                'email' => $agent->email,
                'url' => route('public.account.properties.wizard.sign-contract', ['property' => $property->id]),
            ] : null,
            'admin' => [
                'name' => __('Admin'),
                'email' => setting('admin_email'),
                'url' => route('property.wizard.sign-contract', ['property' => $property->id]),
            ],
        ];

        $signerName = $recipients[$signerRole]['name'] ?? ucfirst($signerRole);

        foreach ($recipients as $targetRole => $info) {
            if ($targetRole === $signerRole || ! $info) {
                continue;
            }

            $this->sendWizardEmail("contract_signed_by_{$signerRole}_{$targetRole}", $info['email'], [
                'recipient_name' => $info['name'],
                'signer_name' => $signerName,
                'property_title' => $property->name,
                'property_url' => $info['url'],
            ]);
        }
    }

    /**
     * Every wizard notification email funnels through here - registered
     * under module 'real-estate' (see RealEstateServiceProvider::boot()) so
     * each template's body and on/off toggle are editable from Admin >
     * Settings > Email, same as every other template in this plugin.
     */
    protected function sendWizardEmail(string $template, ?string $email, array $values): void
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

        EmailHandler::setModule('real-estate')
            ->addVariables(config('plugins.real-estate.wizard-email.variables', []))
            ->setVariableValues($values)
            ->sendUsingTemplate($template, $email, [], false, 'plugins', $subject);
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
     * Whether all three parties have signed the contract - i.e. whether
     * Listing Payment is unlocked.
     */
    protected function isContractFullySigned(Property $property): bool
    {
        return (bool) $property->contract_signed_by_member
            && (bool) $property->contract_signed_by_agent
            && (bool) $property->contract_signed_by_admin;
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
