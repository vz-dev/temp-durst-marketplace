<?php

namespace Pyz\Zed\Integra\Communication\Console;

use Pyz\Zed\Integra\Business\IntegraFacade;
use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method IntegraFacade getFacade()
 */
class ExportOpenConsole extends Console
{

    const COMMAND_NAME = 'integra:export:open-orders';
    const DESCRIPTION = 'Checks which branches use INTEGRA. \
    Creates a CSV file for each branch containing all orders that can be delivered. \
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
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $messenger = $this->getMessenger();

        $messenger->info('Exporting open orders...');

        $branchIds = $this
            ->getFacade()
            ->getBranchIdsThatUseIntegra();

        foreach ($branchIds as $branchId) {
            $messenger->info(sprintf('Starting export for branch #%d...', $branchId));

            $this
                ->getFacade()
                ->exportOpenOrdersForBranch($branchId);

            $messenger->info(sprintf('Export for branch #%d finished', $branchId));
        }

        $messenger->info('All exports finished');

        return static::CODE_SUCCESS;
    }

}
