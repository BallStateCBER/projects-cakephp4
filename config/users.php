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
];
