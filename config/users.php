<?php
return [
    'Users' => [
        'table' => 'Users',
        'Registration' => [
            'active' => false,
        ],
        'Key' => [
            'Session' => [
                'resetPasswordUserId' => 'Users.id',
            ],
        ],
    ],
    'Auth' => [
        'Authenticators' => [
            'Cookie' => [
                'fields' => [
                    'username' => 'email',
                ],
            ],
            'Form' => [
                'fields' => [
                    'username' => 'email',
                ],
            ],
        ],
        'AuthenticationComponent' => [
            'requireIdentity' => false,
            'Form' => [
                'fields' => ['username' => 'email'],
            ],
        ],
        'Identifiers' => [
            'Password' => [
                'fields' => ['username' => 'email'],
                'resolver' => [
                    'className' => 'Authentication.Orm',
                    'finder' => 'all',
                ],
                'passwordHasher' => [
                    'className' => 'Authentication.Fallback',
                    'hashers' => [
                        'Authentication.Default',
                        [
                            'className' => 'Authentication.Legacy',
                            'hashType' => 'sha1',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
