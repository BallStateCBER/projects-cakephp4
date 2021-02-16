<?php
return [
    'DataCenter' => [
        'auth' => [
            'enabled' => true,
            'loginUrl' => [
                'prefix' => false,
                'plugin' => null,
                'controller' => 'Users',
                'action' => 'login',
            ],
            'logoutRedirect' => [
                'prefix' => false,
                'plugin' => null,
                'controller' => 'Users',
                'action' => 'login',
            ],
        ],
        'siteTitle' => 'CBER Projects and Publications',
        'siteUrl' => 'https://projects.cberdata.org',

        // Use full URL or leading slash, e.g. '/img/logo/og_logo.png'
        //'defaultOpenGraphLogoPath' => '',
        //'defaultTwitterLogoPath' => '',

        'openGraphDescription' => 'Ball State University\'s Center for Business and Economic Research is a federally designated state data center, providing public access to official government data and other trusted primary sources. We also provide insight and context for what these numbers mean and publish a wide variety of unbiased, data-driven projects and publications that are useful to policy makers, industry experts, community leadership, consultants, and informed citizens.',
        //'facebookAppId' => '',
        'twitterUsername' => '@BallStateCBER',
        'googleAnalyticsId' => 'G-ZXVVDVTS89',
    ],
];
