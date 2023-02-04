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
class CreateConcreteTimeSlotsConsole extends Console
{
    public const COMMAND_NAME = 'delivery-area:create:concrete-time-slots';
    public const DESCRIPTION = 'This command will create all future concrete time slots fot all active time slots of all active branches that are still within the limit configured in the config';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription(self::DESCRIPTION);
        $this->setAliases(['generate:time-slots', 'da:c:cts']);

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
            ->createFutureConcreteTimeSlots();
    }
}
