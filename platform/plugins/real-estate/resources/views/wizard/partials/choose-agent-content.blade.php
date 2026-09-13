@php
    $agentsData = $nearbyAgents->map(function ($agent) {
        // The admin "Agents" form (AccountForm) actually saves the agent's
        // photo to image_path, not avatar_id/the avatar() MediaFile
        // relation - that field has no admin UI wired to it, so it's null
        // for every real agent. image_path is checked first for that
        // reason; avatar_id is still a defensive fallback. Account::
        // avatar_url itself is never used here since its no-picture
        // fallback returns a generated base64 image as an Intervention
        // Image object (not a plain string), which doesn't survive
        // ->toJson() cleanly - a CSS initials badge is used instead.
        $avatarPath = $agent->image_path ?: (($agent->avatar_id && $agent->avatar->url) ? $agent->avatar->url : null);

        return [
            'id' => $agent->id,
            'name' => $agent->getFullName(),
            'avatar' => $avatarPath ? RvMedia::getImageUrl($avatarPath) : null,
            'initials' => strtoupper(mb_substr($agent->first_name, 0, 1) . mb_substr($agent->last_name, 0, 1)),
            'phone' => $agent->phone,
            'email' => $agent->email,
            'description' => $agent->description,
            'listings' => $agent->no_of_listings($agent->id),
        ];
    })->values();
    $currentAgentId = $property->author_type === \Botble\RealEstate\Models\Account::class ? $property->author_id : null;
@endphp

<link rel="stylesheet" href="{{ asset('themes/real-scout/css/fontawesome.min.css') }}">
<link rel="stylesheet" href="{{ Theme::asset()->url('css/wizard/property-wizard.css') }}">

<div class="property-wizard" data-csrf-token="{{ csrf_token() }}">
    <div class="property-wizard__intro">
        <span class="property-wizard__eyebrow">{{ __('Step 2 of 6') }}</span>
        <h1 class="property-wizard__title">{{ __('Choose Agent') }}</h1>
    </div>

    @include('plugins/real-estate::wizard.partials.global-header', ['currentGlobalStep' => 2])

    <div class="wizard-panel">
        <h2 class="wizard-panel__heading">{{ __('Choose an Agent') }}</h2>
        <p class="wizard-panel__description">{{ __('Pick the agent who will represent ":name".', ['name' => $property->name]) }}</p>

        @if ($agentsData->isEmpty())
            <div class="wizard-placeholder wizard-placeholder--empty">
                <i class="fas fa-user-slash"></i>
                <h2>{{ __('No agents available for this area yet') }}</h2>
                <p class="wizard-hint">{{ __('We don\'t have an agent covering this property\'s location right now. Our team will assign one for you as soon as possible.') }}</p>
            </div>

            <div class="wizard-panel__actions">
                <a href="{{ $showBaseUrl }}?step=4" class="wizard-btn wizard-btn--ghost"><i class="fas fa-arrow-left"></i> {{ __('Back') }}</a>
                <span></span>
            </div>
        @else
            <form data-step-form action="{{ $saveAgentUrl }}" method="post" data-agents="{{ $agentsData->toJson() }}">
                <div class="wizard-field">
                    <label>{{ __('Select an Agent') }}</label>
                    <select class="wizard-select" data-field="agent_id" id="wizard-agent-select">
                        <option value="">{{ __('Choose an agent...') }}</option>
                        @foreach ($agentsData as $agent)
                            <option value="{{ $agent['id'] }}" {{ (string) $currentAgentId === (string) $agent['id'] ? 'selected' : '' }}>{{ $agent['name'] }}</option>
                        @endforeach
                    </select>
                    <div class="wizard-error" data-error-for="agent_id"></div>
                </div>

                <div class="wizard-agent-card" data-agent-card></div>

                <div class="wizard-panel__actions">
                    <a href="{{ $showBaseUrl }}?step=4" class="wizard-btn wizard-btn--ghost"><i class="fas fa-arrow-left"></i> {{ __('Back') }}</a>
                    <button type="submit" class="wizard-btn wizard-btn--primary" data-step-submit data-loading-text="{{ __('Saving...') }}">
                        {{ __('Save & Continue') }} <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>

<script src="{{ Theme::asset()->url('js/wizard/property-wizard.js') }}"></script>
