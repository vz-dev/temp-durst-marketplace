<?php
/**
 * Durst - project - CreateBillingItemConsole.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-27
 * Time: 08:26
 */

namespace Pyz\Zed\Billing\Communication\Console;

use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreateBillingItemConsole
 * @package Pyz\Zed\Billing\Communication\Console
 * @method \Pyz\Zed\Billing\Business\BillingFacadeInterface getFacade()
 */
class CreateBillingItemConsole extends Console
{
    public const COMMAND_NAME = 'billing:create:billing-items';
    public const DESCRIPTION = 'This command will create all billing items for billing which ended the day before';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription(self::DESCRIPTION);
        $this->setAliases(['generate:billing-items']);

        parent::configure();
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this
            ->getFacade()
            ->createBillingItemsForEndedBillingPeriods();
    }
}
