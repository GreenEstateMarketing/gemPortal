@php
    $layouts = [
        'admin' => 'plugins/real-estate::wizard.choose-agent-wrappers.admin',
        'agent' => 'plugins/real-estate::wizard.choose-agent-wrappers.account',
        'member' => 'plugins/real-estate::wizard.choose-agent-wrappers.member',
    ];
@endphp

@include($layouts[$role] ?? $layouts['member'])
