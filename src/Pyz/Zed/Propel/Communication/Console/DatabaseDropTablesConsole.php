<?php

namespace Pyz\Zed\Propel\Communication\Console;

use Exception;
use Propel\Runtime\Connection\Exception\ConnectionException;
use Spryker\Zed\Kernel\Communication\Console\Console;
use Spryker\Zed\Propel\Business\PropelFacadeInterface;
use Spryker\Zed\Propel\Communication\PropelCommunicationFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method PropelFacadeInterface getFacade()
 * @method PropelCommunicationFactory getFactory()
 */
class DatabaseDropTablesConsole extends Console
{
    public const COMMAND_NAME = 'propel:tables:drop';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription('Dropping all database tables, without dropping the database.');

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->info('Dropping all database tables.');

        try {
            $this->getFacade()->dropDatabaseTables();
            $this->info('All database tables have been dropped.');
        } catch (ConnectionException $exception) {
            $this->error('Database is not reachable.');

            return static::CODE_ERROR;
        } catch (Exception $exception) {
            $this->error('Error happened during dropping database tables.');
            $this->error($exception->getMessage());

            return static::CODE_ERROR;
        }

        return static::CODE_SUCCESS;
    }
}
