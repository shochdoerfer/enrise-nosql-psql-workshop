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

use Igorw\Silex\ConfigServiceProvider;
use Nocommerce\Web\ApiControllerProvider;
use Nocommerce\Web\ShopControllerProvider;
use Nocommerce\Web\Twig\NocommerceTwigExtension;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;

$app = new Application();

$app->register(new ConfigServiceProvider(__DIR__ . '/../config/config.php'));

$app->register(new DoctrineServiceProvider(), [
    'db.options' => [
        'driver' => 'pdo_pgsql',
        'host' => $app['database']['host'],
        'dbname' => $app['database']['schema'],
        'user' => $app['database']['username'],
        'password' => $app['database']['password'],
        'port' => $app['database']['port']
    ]
]);

$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__.'/../views'
]);

$app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
    /** @var Twig_Environment $twig */
    $twig->addExtension(new NocommerceTwigExtension($app));
    return $twig;
}));

$app->mount('/api', new ApiControllerProvider());
$app->mount('/', new ShopControllerProvider());

$app->run();
