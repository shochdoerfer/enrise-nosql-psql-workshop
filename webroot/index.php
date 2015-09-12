<?php

/**
 * This file is part of the nocommerce project.
 *
 * (c) bitExpert AG
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Silex\Application;

$app = new Application();

$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__.'/../views',
]);

$app->get('/', function (Application $app) {
    return $app['twig']->render('layout.twig');
});

$app->run();
