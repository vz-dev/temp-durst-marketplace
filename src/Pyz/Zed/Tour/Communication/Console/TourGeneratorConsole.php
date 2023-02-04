<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 18.12.18
 * Time: 10:08
 */

namespace Pyz\Zed\Tour\Communication\Console;


use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TourGeneratorConsole
 * @package Pyz\Zed\Tour\Communication\Console
 * @method \Pyz\Zed\Tour\Business\TourFacadeInterface getFacade()
 */
class TourGeneratorConsole extends Console
{
    public const COMMAND_NAME = 'tour:concrete:generate';
    public const COMMAND_DESCRIPTION = 'Generates Concrete Tours for all existing Concrete Time Slots in future.';

    /**
     * @return void
     */
    protected function configure() : void
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription(self::COMMAND_DESCRIPTION);

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this
            ->getFacade()
            ->generateAllConcreteToursForExistingConcreteTimeSlotsInFuture();
    }

}
