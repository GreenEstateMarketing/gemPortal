<?php

return [
    'name'        => 'plugins/blog::subscribers.settings.email.title',
    'description' => 'plugins/blog::subscribers.settings.email.description',
    'templates'   => [
        'post-published' => [
            'title'       => 'plugins/blog::subscribers.settings.email.templates.post_published_title',
            'description' => 'plugins/blog::subscribers.settings.email.templates.post_published_description',
            'subject'     => 'New article: {{ post_title }}',
            'can_off'     => true,
        ],
    ],
    'variables'   => [
        'post_title'      => 'Post title',
        'post_excerpt'    => 'Post excerpt',
        'post_url'        => 'Post URL',
        'post_image'      => 'Post image URL',
        'unsubscribe_url' => 'Unsubscribe URL',
    ],
];
