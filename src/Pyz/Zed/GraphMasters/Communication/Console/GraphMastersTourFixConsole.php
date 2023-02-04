<?php

namespace Pyz\Zed\GraphMasters\Communication\Console;

use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\GraphMasters\Business\GraphMastersFacadeInterface;
use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method GraphMastersFacadeInterface getFacade()
 */
class GraphMastersTourFixConsole extends Console
{
    public const COMMAND_NAME = 'graphmasters:tours:fix';
    public const DESCRIPTION = 'Fixes Graphmasters tour with given internal ID';

    protected function configure(): void
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription(self::DESCRIPTION)
            ->addArgument('idGraphmastersTour', InputArgument::REQUIRED, 'Internal ID of Graphmasters tour to fix');

        parent::configure();
    }

    /**
     * @throws PropelException
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $idTour = $input->getArgument('idGraphmastersTour');

        $this
            ->getFacade()
            ->fixTourById($idTour);

        return self::CODE_SUCCESS;
    }
}
