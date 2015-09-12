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

/**
 * Category sidebar renderer.
 */
class CategoryRenderer
{
    /**
     * @var Connection
     */
    protected $db;

    /**
     * Creates a new {@link \Nocommerce\Twig\CategoryProvider}.
     *
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * {{@inheritDoc}}
     */
    public function __invoke()
    {
        $content = '';
        $content .= '<a href="/browse/Category1" class="list-group-item">Category 1</a>';
        $content .= '<a href="/browse/Category2" class="list-group-item">Category 2</a>';
        $content .= '<a href="/browse/Category2" class="list-group-item">Category 3</a>';

        return $content;
    }
}
