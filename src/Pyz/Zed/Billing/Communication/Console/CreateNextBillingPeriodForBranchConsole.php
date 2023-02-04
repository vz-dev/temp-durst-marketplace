<?php
/**
 * Durst - project - CreateNextBillingPeriodForBranch.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 28.05.20
 * Time: 09:34
 */

namespace Pyz\Zed\Billing\Communication\Console;


use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreateNextBillingPeriodForBranchConsole
 * @package Pyz\Zed\Billing\Communication\Console
 * @method \Pyz\Zed\Billing\Business\BillingFacadeInterface getFacade()
 */
class CreateNextBillingPeriodForBranchConsole extends Console
{
    protected const COMMAND_NAME = 'billing:create:next-billing-period-for-branch';
    protected const DESCRIPTION = 'This command will create all billing items for billing periods with the given id';

    protected const OPTION_BRANCH_ID = 'branch-id';
    protected const OPTION_BRANCH_ID_DESCRIPTION = 'The ID of the branch for which the next billing period should be created';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription(self::DESCRIPTION);
        $this->setAliases(['create:next-period']);
        $this->addArgument(
            self::OPTION_BRANCH_ID,
            InputArgument::REQUIRED,
            self::OPTION_BRANCH_ID_DESCRIPTION
        );

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
            ->createBillingPeriodForBranch($input->getArgument(self::OPTION_BRANCH_ID));
    }
}
