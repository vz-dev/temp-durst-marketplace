<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2018-12-13
 * Time: 10:14
 */

namespace Pyz\Zed\Tour\Communication\Console;


use Pyz\Zed\Tour\Business\TourFacadeInterface;
use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TourExportCheckConsole
 * @package Pyz\Zed\Tour\Communication\Console
 * @method TourFacadeInterface getFacade()
 */
class TourExportCheckConsole extends Console
{
    protected const COMMAND_NAME = 'tour:export:check';
    protected const COMMAND_DESCRIPTION = 'Check, if concrete tours need to be exported.';

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
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->info(self::COMMAND_DESCRIPTION);

        $countConcreteTourExports = $this
            ->getFacade()
            ->saveConcreteToursToExport();

        if ($countConcreteTourExports > 0) {
            $message = sprintf(
                'Created %d new entries for exporting.',
                $countConcreteTourExports
            );
        } else {
            $message = 'No new entries to export.';
        }

        $this
            ->info($message);

        return 0;
    }
}