<?php
/**
 * Durst - project - CollectorTimeSlotSearchExportConsole.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 27.11.18
 * Time: 17:52
 */

namespace Pyz\Zed\Collector\Communication\Console;


use Spryker\Zed\Collector\Communication\Console\AbstractCollectorConsole;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CollectorTimeSlotSearchExportConsole
 * @package Pyz\Zed\Collector\Communication\Console
 * @method \Pyz\Zed\Collector\Business\CollectorFacadeInterface getFacade()
 */
class CollectorTimeSlotSearchExportConsole extends AbstractCollectorConsole
{
    const COMMAND_NAME = 'collector:time-slot-search:export';
    const COMMAND_DESCRIPTION = 'Collector time slot export search';

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
        $exportResults = $this->getFacade()->exportTimeSlotSearch($output);

        $message = $this->buildNestedSummary($exportResults);
        $message = '<info>' . $message . '</info>';

        $output->write($message);
    }
}