<?php

namespace Pyz\Zed\Integra\Communication\Console;

use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method \Pyz\Zed\Integra\Business\IntegraFacade getFacade()
 */
class ExportClosedConsole extends Console
{

    const COMMAND_NAME = 'integra:export:closed-orders';
    const DESCRIPTION = 'Checks which branches use INTEGRA. \
    Creates a CSV file for each branch containing all orders that are closed. \
    Sends the file to the configured ftp server.';

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

        $messenger->info('Exporting closed orders...');

        $branchIds = $this
            ->getFacade()
            ->getBranchIdsThatUseIntegra();

        foreach ($branchIds as $branchId) {
            $messenger->info(sprintf('Starting export for branch #%d...', $branchId));

            $this
                ->getFacade()
                ->exportClosedOrdersForBranch($branchId);

            $messenger->info(sprintf('Export for branch #%d finished', $branchId));
        }

        $messenger->info('All exports finished');

        return static::CODE_SUCCESS;
    }

}
