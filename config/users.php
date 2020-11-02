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
                'className' => 'Authentication.Cookie',
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
            'loginRedirect' => '/',
            'logoutRedirect' => '/',
            'Form' => [
                'fields' => ['username' => 'email'],
            ],
        ],
        'Identifiers' => [
            'Password' => [
                'className' => 'Authentication.Password',
                'fields' => [
                    'username' => 'email',
                    'password' => 'password',
                ],
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
