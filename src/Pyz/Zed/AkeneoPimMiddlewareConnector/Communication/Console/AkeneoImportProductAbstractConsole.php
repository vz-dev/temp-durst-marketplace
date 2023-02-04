<?php
/**
 * Durst - project - AkeneoImportProductAbstractConsole.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 05.07.18
 * Time: 10:12
 */

namespace Pyz\Zed\AkeneoPimMiddlewareConnector\Communication\Console;


use Pyz\Shared\AkeneoPimMiddlewareConnector\AkeneoPimMiddlewareConnectorConstants;
use Spryker\Shared\Config\Config;
use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class AkeneoImportProductAbstractConsole extends Console
{
    const COMMAND_NAME = 'akeneo:import:product-abstract';
    const COMMAND_DESCRIPTION = 'Import product model data from mapped JSON file';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription(self::COMMAND_DESCRIPTION);

        parent::configure();
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->info(self::COMMAND_DESCRIPTION);

        $productModelFilePath = Config::get(AkeneoPimMiddlewareConnectorConstants::PRODUCT_MODEL_MAP_FILE_PATH);
        $command = 'APPLICATION_ENV=' . APPLICATION_ENV
            . ' APPLICATION_STORE=' . APPLICATION_STORE
            . ' APPLICATION_ROOT_DIR=' . APPLICATION_ROOT_DIR
            . ' APPLICATION=' . APPLICATION
            . ' vendor/bin/console middleware:process:run -p PRODUCT_MODEL_IMPORT_PROCESS '
            . '-i ' . $productModelFilePath;

        $process = new Process(
            $command,
            APPLICATION_ROOT_DIR,
            null,
            null,
            6000
        );

        return $process->run(function ($type, $buffer) {
            echo $buffer;
        });
    }
}