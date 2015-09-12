<?php

/**
 * This file is part of the nocommerce project.
 *
 * (c) bitExpert AG
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Nocommerce\Web;

use Silex\Application;
use Silex\ControllerProviderInterface;

/**
 * Controller Provider for api endpoints.
 */
class ApiControllerProvider implements ControllerProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        // creates a new controller based on the default route
        $controllers = $app['controllers_factory'];

        $controllers->get('/products', function (Application $app) {
            return $app->json([]);
        });

        $controllers->get('/product/{productId}', function (Application $app, $productId) {
            return $app->json([]);
        });

        return $controllers;
    }
}
