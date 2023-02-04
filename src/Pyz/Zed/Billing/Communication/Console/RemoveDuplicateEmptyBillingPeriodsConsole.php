<?php

namespace Pyz\Zed\Billing\Communication\Console;

use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\Billing\Business\BillingFacadeInterface;
use Spryker\Zed\Kernel\Communication\Console\Console;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method BillingFacadeInterface getFacade()
 */
class RemoveDuplicateEmptyBillingPeriodsConsole extends Console
{
    public const COMMAND_NAME = 'billing:remove:duplicate-empty-billing-periods';
    public const DESCRIPTION = 'This command will remove all duplicate empty billing periods for the branch with the specified ID';

    public const OPTION_BRANCH_ID = 'branch_id';
    public const OPTION_BRANCH_ID_DESCRIPTION = 'The ID of the branch';

    protected function configure(): void
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription(self::DESCRIPTION)
            ->addArgument(
                self::OPTION_BRANCH_ID,
                InputArgument::REQUIRED,
                self::OPTION_BRANCH_ID_DESCRIPTION
            );

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws PropelException
     * @throws AmbiguousComparisonException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fkBranch = $input->getArgument(self::OPTION_BRANCH_ID);

        $this
            ->getFacade()
            ->removeDuplicateEmptyBillingPeriodsForBranch($fkBranch);

        return Console::CODE_SUCCESS;
    }
}
