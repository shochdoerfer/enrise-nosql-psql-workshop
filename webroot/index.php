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

use Nocommerce\Web\ApiControllerProvider;
use Nocommerce\Web\ShopControllerProvider;
use Nocommerce\Web\Twig\NocommerceTwigExtension;
use Silex\Application;

$app = new Application();

$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__.'/../views',
]);

$app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
    $twig->addExtension(new NocommerceTwigExtension());
    return $twig;
}));

$app->mount('/api', new ApiControllerProvider());
$app->mount('/', new ShopControllerProvider());

$app->run();
