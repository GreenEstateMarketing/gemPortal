<?php

return [
    'name' => 'Agent Applications',
    'edit' => 'View Application',
    'application_information' => 'Application Information',
    'time' => 'Submitted At',
    'expertise' => 'Expertise in Areas',
    'resume' => 'Resume',
    'phone' => 'Phone',
    'education_level' => 'Highest Education',
    'years_of_experience' => 'Years of Experience',
    'statuses' => [
        'pending' => 'Pending',
        'reviewed' => 'Reviewed',
    ],
    'form' => [
        'name' => [
            'required' => 'Name is required',
        ],
        'email' => [
            'required' => 'Email is required',
            'email' => 'The email address is not valid',
        ],
        'phone' => [
            'required' => 'Phone number is required',
            'regex' => 'The phone number format is invalid. It must be a valid international number, e.g., +1234567890.',
        ],
        'education_level' => [
            'required' => 'Highest education is required',
        ],
        'years_of_experience' => [
            'required' => 'Years of experience is required',
        ],
        'category_ids' => [
            'required' => 'Please select at least one area of expertise',
        ],
        'resume' => [
            'required' => 'Please upload your resume',
            'mimes' => 'The resume must be a PDF or Word document (.pdf, .doc, .docx)',
            'max' => 'The resume may not be larger than 5MB',
        ],
        'g-recaptcha-response' => [
            'required' => 'Please confirm you are not a robot before submitting.',
            'captcha' => 'You are not confirmed as a robot yet.',
        ],
    ],
];
