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
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller Provider for the default shop functionality.
 */
class ShopControllerProvider implements ControllerProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        // creates a new controller based on the default route
        $controllers = $app['controllers_factory'];

        $controllers->get('/', function (Application $app) {
            $sql = 'SELECT "id", "name", "description", "price", "categories" FROM "products"';
            $products = $app['db']->fetchAll($sql);

            return $app['twig']->render('index.twig', ['products' => $products]);
        });

        $controllers->get('/browse/{category}', function (Application $app, $category) {
            $sql = 'SELECT "id", "name", "description", "price", "categories" FROM "products" WHERE "categories" @> ARRAY[?]';
            $stmt = $app['db']->prepare($sql);
            $stmt->bindValue(1, $category, 'string');
            $stmt->execute();
            $products = $stmt->fetchAll();

            return $app['twig']->render('index.twig', ['products' => $products]);
        });

        $controllers->post('/search', function (Application $app, Request $request) {
            $query = $request->get('query');
            $products = [];

            return $app['twig']->render('index.twig', ['products' => $products]);
        });

        $controllers->post('/rate', function (Application $app) {
            $rating = ['cnt' => 0, 'rating' => 0];

            return $app['twig']->render('ratings.twig', ['rating' => $rating]);
        });

        return $controllers;
    }
}
