<?php
/**
 * Durst - project - CollectorGMTimeSlotSearchExportConsole.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 10.10.21
 * Time: 21:49
 */

namespace Pyz\Zed\Collector\Communication\Console;

use Spryker\Zed\Collector\Communication\Console\AbstractCollectorConsole;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CollectorGMTimeSlotSearchExportConsole
 * @package Pyz\Zed\Collector\Communication\Console
 * @method \Pyz\Zed\Collector\Business\CollectorFacadeInterface getFacade()
 */
class CollectorGMTimeSlotSearchExportConsole extends AbstractCollectorConsole
{
    const COMMAND_NAME = 'collector:gm-time-slot-search:export';
    const COMMAND_DESCRIPTION = 'Collector graphmasters time slot export search';

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
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $exportResults = $this->getFacade()->exportGMTimeSlotSearch($output);

        $message = $this->buildNestedSummary($exportResults);
        $message = '<info>' . $message . '</info>';

        $output->write($message);
    }
}
