@php
    $layouts = [
        'admin' => 'plugins/real-estate::wizard.sign-contract-wrappers.admin',
        'agent' => 'plugins/real-estate::wizard.sign-contract-wrappers.account',
        'member' => 'plugins/real-estate::wizard.sign-contract-wrappers.member',
    ];
@endphp

@include($layouts[$role] ?? $layouts['member'])
