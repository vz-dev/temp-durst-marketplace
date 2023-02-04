<?php

namespace Pyz\Zed\Oms\Communication\Console;

use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\Oms\Business\OmsFacade;
use Pyz\Zed\Oms\Communication\OmsCommunicationFactory;
use Spryker\Zed\Kernel\Communication\Console\Console;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method OmsFacade getFacade()
 * @method OmsCommunicationFactory getFactory()
 */
class DetectStuckOrders extends Console
{
    public const COMMAND_NAME = 'oms:detect-stuck-orders';
    public const DESCRIPTION = 'Detects orders which are stuck in certain states and sends a notification e-mail';

    protected function configure(): void
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription(self::DESCRIPTION);

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     *
     * @throws AmbiguousComparisonException
     * @throws ContainerKeyNotFoundException
     * @throws PropelException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this
            ->getFacade()
            ->detectStuckOrders();

        return Console::CODE_SUCCESS;
    }
}
