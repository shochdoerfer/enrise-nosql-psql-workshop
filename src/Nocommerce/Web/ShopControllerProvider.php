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

        // helper function to json_decode rating and meta fields
        $decodeJsonAttributes = function (array $products) {
            // "converting" the json strings for rating and meta into native php datatypes
            foreach ($products as $key => $product) {
                $product['rating'] = json_decode($product['rating']);
                $product['meta'] = json_decode($product['meta']);
                $products[$key] = $product;
            }

            return $products;
        };

        $controllers->get('/', function (Application $app) use ($decodeJsonAttributes) {
            $sql = 'SELECT "id", "name", "description", "price", "categories", "rating", "meta" FROM "products"';
            $products = $app['db']->fetchAll($sql);
            $products = $decodeJsonAttributes($products);

            return $app['twig']->render('index.twig', ['products' => $products]);
        });

        $controllers->get('/browse/{category}', function (Application $app, $category) use ($decodeJsonAttributes) {
            $sql = 'SELECT "id", "name", "description", "price", "categories", "rating", "meta" FROM "products" WHERE "categories" @> ARRAY[?]';
            $stmt = $app['db']->prepare($sql);
            $stmt->bindValue(1, $category, 'string');
            $stmt->execute();
            $products = $stmt->fetchAll();
            $products = $decodeJsonAttributes($products);

            return $app['twig']->render('index.twig', ['products' => $products]);
        });

        $controllers->post('/search', function (Application $app, Request $request) use ($decodeJsonAttributes) {
            $query = $request->get('query');
            $sql = 'SELECT "id", "name", "description", "price", "categories", "rating", "meta" FROM "products" WHERE to_tsvector(\'english\',  "name" || \' \' || "description") @@ to_tsquery(\'english\', ?)';
            $stmt = $app['db']->prepare($sql);
            $stmt->bindValue(1, $query, 'string');
            $stmt->execute();
            $products = $stmt->fetchAll();
            $products = $decodeJsonAttributes($products);

            return $app['twig']->render('index.twig', ['products' => $products]);

        });

        $controllers->post('/rate', function (Application $app, Request $request) use ($decodeJsonAttributes) {
            $productId = (int) $request->get('productId');
            $rating = $request->get('rating');
            $rating = ['cnt' => 0, 'rating' => 0];

            return $app['twig']->render('ratings.twig', ['rating' => $rating]);
        });

        return $controllers;
    }
}
