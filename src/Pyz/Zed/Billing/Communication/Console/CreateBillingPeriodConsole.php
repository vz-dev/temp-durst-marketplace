<?php

namespace Pyz\Zed\Billing\Communication\Console;

use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method \Pyz\Zed\Billing\Business\BillingFacadeInterface getFacade()
 */
class CreateBillingPeriodConsole extends Console
{
    public const COMMAND_NAME = 'billing:create:billing-periods';
    public const DESCRIPTION = 'This command will create all billing periods that are about to end(using days in advance setting from config) or need to be newly created';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription(self::DESCRIPTION);
        $this->setAliases(['generate:billing-periods']);

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this
            ->getFacade()
            ->createBillingPeriods();
    }

}
