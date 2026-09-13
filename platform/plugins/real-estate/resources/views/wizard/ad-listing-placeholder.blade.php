@php
    $layouts = [
        'admin' => 'plugins/real-estate::wizard.ad-listing-wrappers.admin',
        'agent' => 'plugins/real-estate::wizard.ad-listing-wrappers.account',
        'member' => 'plugins/real-estate::wizard.ad-listing-wrappers.member',
    ];
@endphp

@include($layouts[$role] ?? $layouts['member'])
