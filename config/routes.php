<?php
/**
 * Routes configuration.
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * It's loaded within the context of `Application::routes()` method which
 * receives a `RouteBuilder` instance `$routes` as method argument.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;

/*
 * The default class to use for all routes
 *
 * The following route classes are supplied with CakePHP and are appropriate
 * to set as the default:
 *
 * - Route
 * - InflectedRoute
 * - DashedRoute
 *
 * If no call is made to `Router::defaultRouteClass()`, the class used is
 * `Route` (`Cake\Routing\Route\Route`)
 *
 * Note that `Route` does not do any inflections on URLs which will result in
 * inconsistently cased URLs when used with `:plugin`, `:controller` and
 * `:action` markers.
 */
/** @var \Cake\Routing\RouteBuilder $routes */
$routes->setRouteClass(DashedRoute::class);

$routes->scope('/', function (RouteBuilder $builder) {
    $builder->setExtensions(['json']);

    // Releases
    $builder->connect('/', ['controller' => 'Releases', 'action' => 'index']);
    $builder->connect(
        '/{id}/{slug}',
        ['controller' => 'Releases', 'action' => 'view'],
        ['id' => '[0-9]+', 'slug' => '[-_a-z0-9]+', 'pass' => ['id', 'slug']],
    );
    $builder->connect(
        '/year/{year}',
        ['controller' => 'Releases', 'action' => 'year'],
        ['year' => '[0-9]+', 'pass' => ['year']],
    );
    $builder->connect('/reports/*', ['controller' => 'Releases', 'action' => 'reportNotFound']);

    // Partners
    $builder->connect(
        '/partner/{id}/{slug}',
        ['controller' => 'Partners', 'action' => 'view'],
        ['id' => '[0-9]+', 'slug' => '[-_a-z0-9]+', 'pass' => ['id', 'slug']],
    );

    // Tags
    $builder->connect(
        '/tag/{id}/{slug}',
        ['controller' => 'Tags', 'action' => 'view'],
        ['id' => '[0-9]+', 'slug' => '[-_a-z0-9]+', 'pass' => ['id', 'slug']],
    );

    // Authors
    $builder->connect(
        '/author/{id}',
        ['controller' => 'Authors', 'action' => 'view'],
        ['id' => '[0-9]+', 'pass' => ['id']],
    );

    // Users
    $builder->connect(
        '/login',
        ['controller' => 'Users', 'action' => 'login'],
    );
    $builder->connect(
        '/logout',
        ['controller' => 'Users', 'action' => 'logout'],
    );
    $builder->connect(
        '/request-reset-password',
        ['controller' => 'Users', 'action' => 'requestResetPassword']
    );
    $builder->connect(
        '/reset-password/{token}',
        ['controller' => 'Users', 'action' => 'resetPassword'],
        ['pass' => ['token']]
    );

    $builder->fallbacks();
});

/*
 * If you need a different set of middleware or none at all,
 * open new scope and define routes there.
 *
 * ```
 * $routes->scope('/api', function (RouteBuilder $builder) {
 *     // No $builder->applyMiddleware() here.
 *     // Connect API actions here.
 * });
 * ```
 */
