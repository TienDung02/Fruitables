<?php

return [
    'email' => [
        'account_exists' => [
            'subject' => 'Account Already Exists',
            'message' => 'Thank you for your request to create an account on our website.',
            'notice' => 'We have noticed that this email address is already associated with an account. Please',
            'link_text' => 'log in',
            'link_suffix' => 'using your existing details.',
            'project_info_text' => 'This website is built for <strong>educational and research</strong> purposes only and is not intended for commercial use. If you are interested in the scope of the project or would like to learn more about how security-related feedback is handled, please refer to the link below for further details.',
            'project_info_button' => 'Project Information',
            'footer_text' => 'If you did not request this registration, you can safely ignore this email.',
        ],
        'account_not_exists' => [
            'subject' => 'Account not found',
            'message' => 'Thank you for your interest in our website.',
            'notice' => 'We noticed that this email address is not yet registered. Please',
            'link_text' => 'create an account',
            'link_suffix' => 'to continue.',
            'project_info_text' => 'This website is built for <strong>educational and research</strong> purposes only and is not intended for commercial use. If you are interested in the scope of the project or would like to learn more about how security-related feedback is handled, please refer to the link below for further details.',
            'project_info_button' => 'Project information',
            'footer_text' => 'If you did not request this action, you may safely ignore this email.',
        ],


        'complete_action' => [
            'register' => [
                'subject' => 'Complete Your Registration',
                'title' => 'Complete Your Registration',
                'message' => 'Thank you for your request to register with our website.',
                'instruction' => 'Please click the button below to complete your registration.',
                'button' => 'Complete Registration',
            ],

            'password_reset' => [
                'subject' => 'Reset Your Password',
                'title' => 'Reset Your Password',
                'message' => 'Thank you for your request to reset your password.',
                'instruction' => 'Please click the button below to reset your password.',
                'button' => 'Reset Password',
            ],

            'project_info_text' => 'This website is built for <strong>educational and research</strong> purposes only and is not intended for commercial use. If you are interested in the scope of the project or would like to learn more about how security-related feedback is handled, please refer to the link below for further details.',
            'project_info_button' => 'Project Information',
            'footer_text' => 'If you did not request this action, you can safely ignore this email.',
        ],
    ],
];
