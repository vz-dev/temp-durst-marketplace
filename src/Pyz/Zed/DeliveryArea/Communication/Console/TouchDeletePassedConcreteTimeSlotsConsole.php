<?php
/**
 * Durst - project - CreateConcreteTimeSlotsConsole.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 22.11.18
 * Time: 14:51
 */

namespace Pyz\Zed\DeliveryArea\Communication\Console;

use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreateConcreteTimeSlotsConsole
 * @package Pyz\Zed\DeliveryArea\Communication\Console
 * @method \Pyz\Zed\DeliveryArea\Business\DeliveryAreaFacadeInterface getFacade()
 */
class TouchDeletePassedConcreteTimeSlotsConsole extends Console
{
    public const COMMAND_NAME = 'delivery-area:delete-touch:passed-concrete-time-slots';
    public const DESCRIPTION = 'This command will touch all concrete time slots that lay in the past so they will be removed from elasticsearch';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription(self::DESCRIPTION);
        $this->setAliases(['delete:time-slots', 'da:dt:pcts']);

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
        $this
            ->getFacade()
            ->touchDeletePassedConcreteTimeSlots();
    }
}
