<?php

namespace Pyz\Zed\GraphMasters\Communication\Console;

use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\GraphMasters\Business\GraphMastersFacadeInterface;
use Spryker\Zed\Kernel\Communication\Console\Console;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method GraphMastersFacadeInterface getFacade()
 */
class GraphMastersTourImportConsole extends Console
{
    public const COMMAND_NAME = 'graphmasters:tours:import';
    public const DESCRIPTION = 'Imports tours from Graphmasters using their API';

    protected function configure(): void
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription(self::DESCRIPTION);

        parent::configure();
    }

    /**
     * @throws AmbiguousComparisonException
     * @throws ContainerKeyNotFoundException
     * @throws PropelException
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $this
            ->getFacade()
            ->importTours();

        return self::CODE_SUCCESS;
    }
}
