<?php
/**
 * Durst - project - AkeneoMapAttributesConsole.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 05.07.18
 * Time: 09:56
 */

namespace Pyz\Zed\AkeneoPimMiddlewareConnector\Communication\Console;


use Spryker\Shared\Config\Config;
use Spryker\Zed\Kernel\Communication\Console\Console;
use SprykerEco\Shared\AkeneoPimMiddlewareConnector\AkeneoPimMiddlewareConnectorConstants;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class AkeneoMapAttributesConsole extends Console
{
    const COMMAND_NAME = 'akeneo:map:attributes';
    const COMMAND_DESCRIPTION = 'Map attributes data from akeneo pim to spryker attributes';

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

        $attributesFilePath = Config::get(AkeneoPimMiddlewareConnectorConstants::ATTRIBUTE_MAP_FILE_PATH);
        $command = 'APPLICATION_ENV=' . APPLICATION_ENV
            . ' APPLICATION_STORE=' . APPLICATION_STORE
            . ' APPLICATION_ROOT_DIR=' . APPLICATION_ROOT_DIR
            . ' APPLICATION=' . APPLICATION
            . ' vendor/bin/console middleware:process:run -p ATTRIBUTE_MAP_PROCESS '
            . '-o ' . $attributesFilePath;

        $process = new Process(
            $command,
            APPLICATION_ROOT_DIR,
            null,
            null,
            3600
        );

        return $process->run(function ($type, $buffer) {
            echo $buffer;
        });
    }
}