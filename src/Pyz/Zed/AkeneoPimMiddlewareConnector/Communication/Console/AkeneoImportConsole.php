<?php
/**
 * Durst - project - AkeneoImportConsole.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 05.07.18
 * Time: 08:43
 */

namespace Pyz\Zed\AkeneoPimMiddlewareConnector\Communication\Console;

use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AkeneoImportConsole extends Console
{
    const COMMAND_NAME = 'akeneo:import';
    const COMMAND_DESCRIPTION = 'Import product data from akeneo pim';

    const OPTION_NO_MAP = 'no-map';
    const OPTION_NO_MAP_SHORTCUT = 'o';
    const OPTION_NO_MAP_DESCRIPTION = 'Runs without downloading data from akeneo pim, just imports JSON files';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription(self::COMMAND_DESCRIPTION);

        $this->addOption(
            self::OPTION_NO_MAP,
            self::OPTION_NO_MAP_SHORTCUT,
            InputOption::VALUE_NONE,
            self::OPTION_NO_MAP_DESCRIPTION
        );

        parent::configure();
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int|null
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dependingCommands = $this->getDependingCommands();

        foreach ($dependingCommands as $commandName) {
            $this->runDependingCommand($commandName);

            if ($this->hasError()) {
                return $this->getLastExitCode();
            }
        }
    }

    /**
     * @param string $command
     * @param array $arguments
     *
     * @return void
     * @throws \Exception
     */
    protected function runDependingCommand($command, array $arguments = [])
    {
        $command = $this->getApplication()->find($command);
        $arguments['command'] = $command;
        $input = new ArrayInput($arguments);
        $command->run($input, $this->output);
    }

    /**
     * @return array
     */
    protected function getDependingCommands()
    {
        $noMapOption = $this->input->getOption(self::OPTION_NO_MAP);

        if ($noMapOption === false) {
            return $this->getCommandsWithMap();
        }

        return $this->getCommandsWithoutMap();
    }

    /**
     * @return array
     */
    protected function getCommandsWithoutMap() : array
    {
        return [
            AkeneoImportCategoriesConsole::COMMAND_NAME,
            AkeneoImportAttributesConsole::COMMAND_NAME,
            AkeneoImportProductAbstractConsole::COMMAND_NAME,
            AkeneoImportProductConcreteConsole::COMMAND_NAME,
        ];
    }

    /**
     * @return array
     */
    protected function getCommandsWithMap() : array
    {
        return [
            AkeneoMapLocalesConsole::COMMAND_NAME,
            AkeneoMapAttributesConsole::COMMAND_NAME,
            AkeneoImportCategoriesConsole::COMMAND_NAME,
            AkeneoImportAttributesConsole::COMMAND_NAME,
            AkeneoMapProductAbstractConsole::COMMAND_NAME,
            AkeneoImportProductAbstractConsole::COMMAND_NAME,
            AkeneoMapProductConcreteConsole::COMMAND_NAME,
            AkeneoImportProductConcreteConsole::COMMAND_NAME,
        ];
    }
}