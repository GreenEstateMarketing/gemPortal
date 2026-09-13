@php
    $memberName = $property->member ? $property->member->full_name : __('the Owner');
    $agentName = $property->author_type === \Botble\RealEstate\Models\Account::class && $property->author
        ? $property->author->getFullName()
        : __('the Agent');
@endphp

<div class="wizard-contract-document">
    <div class="wizard-contract-document__header">
        <h2 class="wizard-contract-document__title">{{ __('Property Listing Agreement') }}</h2>
        <p class="wizard-contract-document__ref">{{ __('Reference') }}: {{ __('Property') }} #{{ $property->id }} &mdash; {{ $property->name }}</p>
    </div>

    <div class="wizard-contract-document__body">
        <p>{{ __('This Property Listing Agreement ("Agreement") is entered into by and between the following parties in connection with the property described below (the "Property"):') }}</p>

        <ul>
            <li><strong>{{ __('Owner') }}:</strong> {{ $memberName }} ("{{ __('Owner') }}")</li>
            <li><strong>{{ __('Agent') }}:</strong> {{ $agentName }} ("{{ __('Agent') }}")</li>
            <li><strong>{{ __('Brokerage') }}:</strong> GEM Listing ("{{ __('Brokerage') }}")</li>
        </ul>

        <p><strong>1. {{ __('Property') }}.</strong> {{ __('The Owner engages the Agent and the Brokerage to market and manage the listing of the property known as') }} "{{ $property->name }}" {{ __('(the "Property") on the GEM Listing platform, on the terms set out in this Agreement.') }}</p>

        <p><strong>2. {{ __('Term') }}.</strong> {{ __('This Agreement takes effect on the date it is fully signed by all parties and continues until the listing is withdrawn, sold, let, or otherwise terminated by mutual written consent.') }}</p>

        <p><strong>3. {{ __('Authority to List') }}.</strong> {{ __('The Owner confirms they have the legal right to list the Property and authorizes the Agent and Brokerage to advertise it, respond to enquiries, and arrange viewings on their behalf.') }}</p>

        <p><strong>4. {{ __('Agent Responsibilities') }}.</strong> {{ __('The Agent agrees to market the Property in good faith, promptly relay offers and enquiries to the Owner, and keep the listing information accurate and up to date.') }}</p>

        <p><strong>5. {{ __('Commission') }}.</strong> {{ __('Commission and fees payable to the Agent and Brokerage will be as agreed separately between the parties and are not altered by the signing of this Agreement.') }}</p>

        <p><strong>6. {{ __('Confidentiality') }}.</strong> {{ __('All parties agree to keep any non-public information shared during this engagement confidential, except where disclosure is required to complete a transaction or by law.') }}</p>

        <p><strong>7. {{ __('Verification') }}.</strong> {{ __('The parties acknowledge that the Property has already been reviewed and verified by both the Agent and GEM Listing\'s administration team prior to this Agreement being presented for signature.') }}</p>

        <p><strong>8. {{ __('Signatures') }}.</strong> {{ __('By signing below, each party confirms they have read, understood, and agree to be bound by the terms of this Agreement. This listing cannot proceed to the next step until all three parties have signed.') }}</p>

        <p class="wizard-contract-document__placeholder-note">{{ __('This is placeholder contract text for development purposes and will be replaced with the final agreed wording.') }}</p>
    </div>
</div>
