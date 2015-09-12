<?php

/**
 * This file is part of the nocommerce project.
 *
 * (c) bitExpert AG
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Nocommerce\Web\Twig;

use Doctrine\DBAL\Connection;
use Silex\Application;
use Twig_Extension;
use Twig_SimpleFunction;

class NocommerceTwigExtension extends Twig_Extension
{
    /**
     * @var Connection
     */
    protected $db;

    /**
     * Creates a new {@link \Nocommerce\Twig\StoreTwigExtension}.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->db = $app['db'];
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'NocommerceTwigExtension';
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return array(
            new Twig_SimpleFunction('categories', new CategoryRenderer($this->db)),
        );
    }
}
