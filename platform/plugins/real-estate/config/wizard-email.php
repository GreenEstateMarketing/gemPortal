<?php

return [
    'name'        => 'Property Wizard Notifications',
    'description' => 'Emails sent to members, agents, and admin as a property moves through the submission wizard.',
    'templates'   => [
        'property_submitted_member' => [
            'title'       => 'Property submitted (to member)',
            'description' => 'Sent to the member confirming their property was submitted.',
            'subject'     => 'Your property "{{ property_title }}" has been submitted',
            'can_off'     => true,
        ],
        'property_submitted_admin' => [
            'title'       => 'Property submitted (to admin)',
            'description' => 'Sent to admin when a member submits a new property.',
            'subject'     => 'New property submitted: {{ property_title }}',
            'can_off'     => true,
        ],
        'property_updated_member' => [
            'title'       => 'Property updated (to member)',
            'description' => 'Sent to the member when their already-submitted property is edited through the wizard again.',
            'subject'     => 'Your property "{{ property_title }}" has been updated',
            'can_off'     => true,
        ],
        'property_updated_agent' => [
            'title'       => 'Property updated (to agent)',
            'description' => 'Sent to the agent when a property they manage is edited through the wizard again.',
            'subject'     => 'Property "{{ property_title }}" has been updated',
            'can_off'     => true,
        ],
        'agent_assigned_member' => [
            'title'       => 'Agent assigned (to member)',
            'description' => 'Sent to the member when an agent is assigned to their property.',
            'subject'     => 'An agent has been assigned to "{{ property_title }}"',
            'can_off'     => true,
        ],
        'agent_assigned_agent' => [
            'title'       => 'Agent assigned (to agent)',
            'description' => 'Sent to the agent when they are assigned to a property.',
            'subject'     => 'You have been assigned to "{{ property_title }}"',
            'can_off'     => true,
        ],
        'agent_assigned_admin' => [
            'title'       => 'Agent assigned (to admin)',
            'description' => 'Sent to admin when an agent is assigned to a property.',
            'subject'     => 'Agent assigned to {{ property_title }}',
            'can_off'     => true,
        ],
        'property_verified_by_agent_member' => [
            'title'       => 'Verified by agent (to member)',
            'description' => 'Sent to the member when their agent verifies the property.',
            'subject'     => 'Your property "{{ property_title }}" was verified by your agent',
            'can_off'     => true,
        ],
        'property_verified_by_agent_admin' => [
            'title'       => 'Verified by agent (to admin)',
            'description' => 'Sent to admin when an agent verifies a property.',
            'subject'     => 'Property verified by agent: {{ property_title }}',
            'can_off'     => true,
        ],
        'property_verified_by_admin_member' => [
            'title'       => 'Verified by admin (to member)',
            'description' => 'Sent to the member when admin gives final verification.',
            'subject'     => 'Your property "{{ property_title }}" is fully verified',
            'can_off'     => true,
        ],
        'property_verified_by_admin_agent' => [
            'title'       => 'Verified by admin (to agent)',
            'description' => 'Sent to the agent when admin gives final verification.',
            'subject'     => 'Property "{{ property_title }}" is fully verified',
            'can_off'     => true,
        ],
        'contract_signed_by_member_agent' => [
            'title'       => 'Contract signed by member (to agent)',
            'description' => 'Sent to the agent when the member signs the contract.',
            'subject'     => '{{ signer_name }} signed the contract for "{{ property_title }}"',
            'can_off'     => true,
        ],
        'contract_signed_by_member_admin' => [
            'title'       => 'Contract signed by member (to admin)',
            'description' => 'Sent to admin when the member signs the contract.',
            'subject'     => '{{ signer_name }} signed the contract for "{{ property_title }}"',
            'can_off'     => true,
        ],
        'contract_signed_by_agent_member' => [
            'title'       => 'Contract signed by agent (to member)',
            'description' => 'Sent to the member when the agent signs the contract.',
            'subject'     => '{{ signer_name }} signed the contract for "{{ property_title }}"',
            'can_off'     => true,
        ],
        'contract_signed_by_agent_admin' => [
            'title'       => 'Contract signed by agent (to admin)',
            'description' => 'Sent to admin when the agent signs the contract.',
            'subject'     => '{{ signer_name }} signed the contract for "{{ property_title }}"',
            'can_off'     => true,
        ],
        'contract_signed_by_admin_member' => [
            'title'       => 'Contract signed by admin (to member)',
            'description' => 'Sent to the member when admin signs the contract.',
            'subject'     => '{{ signer_name }} signed the contract for "{{ property_title }}"',
            'can_off'     => true,
        ],
        'contract_signed_by_admin_agent' => [
            'title'       => 'Contract signed by admin (to agent)',
            'description' => 'Sent to the agent when admin signs the contract.',
            'subject'     => '{{ signer_name }} signed the contract for "{{ property_title }}"',
            'can_off'     => true,
        ],
        'listing_payment_confirmed_member' => [
            'title'       => 'Listing payment confirmed (to member)',
            'description' => 'Sent to the member when their listing payment goes through.',
            'subject'     => 'Your property "{{ property_title }}" is now listed',
            'can_off'     => true,
        ],
    ],
    'variables' => [
        'recipient_name' => 'Recipient name',
        'member_name'    => 'Member name',
        'agent_name'     => 'Agent name',
        'signer_name'    => 'Signer name',
        'property_title' => 'Property title',
        'property_url'   => 'Property URL',
    ],
];
