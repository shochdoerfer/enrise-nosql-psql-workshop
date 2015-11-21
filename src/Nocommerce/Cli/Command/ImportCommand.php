<?php

/**
 * This file is part of the nocommerce project.
 *
 * (c) bitExpert AG
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Nocommerce\Cli\Command;

use Doctrine\DBAL\Connection;
use Igorw\Silex\ConfigServiceProvider;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use SimpleXMLElement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console Command to run the import script.
 */
class ImportCommand extends Command
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('nocommerce.import')
            ->setDescription('XML file importer')
            ->setDefinition(
                array(
                    new InputArgument('file', InputArgument::REQUIRED, 'the xml file to import'),
                )
            );
    }

    /**
     * {@inheritDoc}
     * @throws \InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');
        if (!file_exists($file) or !is_readable($file)) {
            $output->write(sprintf('File "%s" not found or not readable!', $file));
            return;
        }

        $db = $this->getDatabaseConnection();
        $products = simplexml_load_file($file);
        if (false !== $products) {
            foreach ($products as $product) {
                /** @var SimpleXmlElement $product */
                $attributes = (array) $product->attributes();
                $attributes = isset($attributes['@attributes']) ? $attributes['@attributes'] : [];
                $categories = [];
                foreach ($product->category as $category) {
                    /** @var SimpleXmlElement $category */
                    $categories[] = (string) $category['name'];
                }

                $id = isset($attributes['id']) ? $attributes['id'] : '';
                $name = isset($attributes['name']) ? $attributes['name'] : '';
                $description = isset($attributes['description']) ? $attributes['description'] : '';
                $price = isset($attributes['price']) ? $attributes['price'] : '';
                unset($attributes['id'], $attributes['name'], $attributes['description'], $attributes['price']);

                // type juggeling
                foreach ($attributes as $key => $value) {
                    if (is_numeric($value)) {
                        $value = floatval($value);
                        if (round($value, 0) == $value) {
                            $value = intval($value);
                        }
                    } else {
                        $valueLower = strtolower($value);
                        if ('true' === $valueLower) {
                            $value = true;
                        } elseif ('false' === $valueLower) {
                            $value = false;
                        }
                    }
                    $attributes[$key] = $value;
                }

                $sql = 'INSERT INTO "products" ("id", "name", "description", "price","meta", "categories") VALUES (?, ?, ?, ?, ?, ?) ON CONFLICT ON CONSTRAINT products_pkey DO UPDATE SET "name" = EXCLUDED.name, "description" = EXCLUDED.description, "price" = EXCLUDED.price, "meta" = EXCLUDED.meta, "categories" = EXCLUDED.categories;';
                $stmt = $db->prepare($sql);
                $stmt->bindValue(1, $id, 'integer');
                $stmt->bindValue(2, $name, 'string');
                $stmt->bindValue(3, $description, 'string');
                $stmt->bindValue(4, $price, 'float');
                $stmt->bindValue(5, json_encode($attributes), 'string');
                $stmt->bindValue(6, sprintf("{%s}", implode(',', $categories)), 'string');
                $stmt->execute();

                // ON CONFLICT DO NOTHING seems to be ignored for foreign tables!
                $sql = 'INSERT INTO "redis_db0" ("key", "value") VALUES (?, ?) ON CONFLICT DO NOTHING;';
                $stmt = $db->prepare($sql);
                $stmt->bindValue(1, $id, 'integer');
                $stmt->bindValue(2, json_encode(['cnt' => 0, 'rating' => 0]), 'string');
                $stmt->execute();
            }

            // ignore any existing entry
            $sql = 'INSERT INTO "imports" ("filename", "contents") VALUES (?, ?) ON CONFLICT DO NOTHING;';
            $stmt = $db->prepare($sql);
            $stmt->bindValue(1, $file, 'string');
            $stmt->bindValue(2, file_get_contents($file), 'string');
            $stmt->execute();
        }
    }

    /**
     * Returns configured {@link \Doctrine\DBAL\Connection}.
     *
     * @return Connection
     */
    protected function getDatabaseConnection()
    {
        $app = new Application();

        $app->register(new ConfigServiceProvider(__DIR__ . '/../../../../config/config.php'));

        $app->register(
            new DoctrineServiceProvider(),
            [
                'db.options' => [
                    'driver' => 'pdo_pgsql',
                    'host' => $app['database']['host'],
                    'dbname' => $app['database']['schema'],
                    'user' => $app['database']['username'],
                    'password' => $app['database']['password'],
                    'port' => $app['database']['port']
                ]
            ]
        );

        return $app['db'];
    }
}
