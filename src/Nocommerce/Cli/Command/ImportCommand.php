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

        $products = simplexml_load_file($file);
        if (false !== $products) {
            foreach ($products as $product) {

            }
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
