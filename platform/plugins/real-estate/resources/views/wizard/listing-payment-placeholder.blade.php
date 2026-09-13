@php
    $layouts = [
        'admin' => 'plugins/real-estate::wizard.listing-payment-wrappers.admin',
        'agent' => 'plugins/real-estate::wizard.listing-payment-wrappers.account',
        'member' => 'plugins/real-estate::wizard.listing-payment-wrappers.member',
    ];
@endphp

@include($layouts[$role] ?? $layouts['member'])
