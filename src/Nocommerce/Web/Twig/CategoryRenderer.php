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
        $sql = 'SELECT UNNEST("categories") as "title", COUNT(*) as "cnt" FROM "products" GROUP BY "title";';
        $categories = $this->db->fetchAll($sql);

        foreach($categories as $category) {
            $content .= '<a href="/browse/'.htmlentities($category['title']).'" class="list-group-item">'.$category['title'].' ('.$category['cnt'].')</a>';
        }

        return $content;
    }
}
