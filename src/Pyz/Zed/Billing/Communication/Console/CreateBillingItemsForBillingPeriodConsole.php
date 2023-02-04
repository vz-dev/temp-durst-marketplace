<?php
/**
 * Durst - project - CreateBillingItemsForBillingPeriodConsole.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-04-05
 * Time: 07:42
 */

namespace Pyz\Zed\Billing\Communication\Console;


use Pyz\Zed\Billing\Business\BillingFacadeInterface;
use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreateBillingItemsForBillingPeriodConsole
 * @package Pyz\Zed\Billing\Communication\Console
 *
 * @method BillingFacadeInterface getFacade()
 */
class CreateBillingItemsForBillingPeriodConsole extends Console
{
    public const COMMAND_NAME = 'billing:create:billing-items-for-billing-period';
    public const DESCRIPTION = 'This command will create all billing items for billing periods with the given id';

    public const OPTION_BILLING_PERIOD_ID = 'billing_period-id';
    public const OPTION_BILLING_PERIOD_ID_DESCRIPTION = 'The Id of the Billing Period for which billing items should be created';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription(self::DESCRIPTION);
        $this->setAliases(['generate:billing-items-for-period']);
        $this->addArgument(self::OPTION_BILLING_PERIOD_ID, InputArgument::REQUIRED, self::OPTION_BILLING_PERIOD_ID_DESCRIPTION);

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->info(self::DESCRIPTION);

        $this
            ->getFacade()
            ->createBillingItemsForBillingPeriodByBillingPeriodId($input->getArgument(self::OPTION_BILLING_PERIOD_ID));
    }
}
