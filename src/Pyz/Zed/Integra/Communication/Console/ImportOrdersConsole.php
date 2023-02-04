<?php

namespace Pyz\Zed\Integra\Communication\Console;

use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method \Pyz\Zed\Integra\Business\IntegraFacade getFacade()
 */
class ImportOrdersConsole extends Console
{
    const COMMAND_NAME = 'integra:import:orders';
    const DESCRIPTION = 'Checks which branches use INTEGRA. \
    Executes orders query via web service for each branch. \
    Persists all orders received.';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName(static::COMMAND_NAME)
            ->setDescription(static::DESCRIPTION);
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $messenger = $this->getMessenger();

        $messenger->info('Importing orders...');

        $branchIds = $this
            ->getFacade()
            ->getBranchIdsThatUseIntegra();

        foreach ($branchIds as $branchId) {
            $messenger->info(sprintf('Starting import for branch #%d...', $branchId));

            $this
                ->getFacade()
                ->importOrdersForBranch($branchId);

            $messenger->info(sprintf('Import for branch #%d finished', $branchId));
        }

        $messenger->info('All imports finished');

        return static::CODE_SUCCESS;
    }
}
