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
            $sql = 'SELECT array_to_json(array_agg(row_to_json("products"))) as formatted_json FROM "products"';
            $json = $app['db']->fetchColumn($sql);
            if(!$json) {
                $json = '[]';
            }

            return $json;
        });

        $controllers->get('/product/{productId}', function (Application $app, $productId) {
            $sql = 'SELECT row_to_json("products") as formatted_json FROM "products" WHERE "id" = ?';
            $stmt = $app['db']->prepare($sql);
            $stmt->bindValue(1, $productId, 'integer');
            $stmt->execute();
            $json = $stmt->fetchColumn();
            if(!$json) {
                $json = '{}';
            }

            return $json;
        });

        return $controllers;
    }
}
