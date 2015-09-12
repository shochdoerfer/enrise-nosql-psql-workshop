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

$app->get('/', function () {
    return 'Nocommerce';
});

$app->run();
