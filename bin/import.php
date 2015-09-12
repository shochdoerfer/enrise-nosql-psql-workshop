#!/usr/bin/env php
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

use Nocommerce\Cli\Application;
use Nocommerce\Cli\Command\ImportCommand;

$cli = new Application(new ImportCommand());
$cli->run();
