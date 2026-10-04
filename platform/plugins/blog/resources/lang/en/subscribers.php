<?php

return [
    'menu'          => 'Newsletter Subscribers',
    'menu_name'     => 'Newsletter Subscribers',
    'name'          => 'Email',
    'created_at'    => 'Subscribed at',
    'status'        => 'Status',
    'deleted'       => 'Subscriber deleted',
    'cannot_delete' => 'Subscriber could not be deleted',
    'notices'       => [
        'no_select' => 'Please select at least one subscriber to take this action!',
    ],
    'settings'      => [
        'email' => [
            'title'       => 'Blog',
            'description' => 'Email template for blog newsletter notifications',
            'templates'   => [
                'post_published_title'       => 'New post published',
                'post_published_description' => 'Sent to every newsletter subscriber when a new blog post is published',
            ],
        ],
    ],
];
