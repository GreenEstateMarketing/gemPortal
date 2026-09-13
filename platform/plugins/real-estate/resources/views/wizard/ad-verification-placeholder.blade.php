@php
    $layouts = [
        'admin' => 'plugins/real-estate::wizard.ad-verification-wrappers.admin',
        'agent' => 'plugins/real-estate::wizard.ad-verification-wrappers.account',
        'member' => 'plugins/real-estate::wizard.ad-verification-wrappers.member',
    ];
@endphp

@include($layouts[$role] ?? $layouts['member'])
