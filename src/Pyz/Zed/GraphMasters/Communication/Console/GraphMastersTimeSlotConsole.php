<?php

namespace Pyz\Zed\GraphMasters\Communication\Console;

use Pyz\Zed\GraphMasters\Business\GraphMastersFacadeInterface;
use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method GraphMastersFacadeInterface getFacade()
 */
class GraphMastersTimeSlotConsole extends Console
{
    public const COMMAND_NAME = 'graphmasters:time-slots:generate';
    public const DESCRIPTION = 'This command will generate future time slots in 15 min intervals/sizes for the next 14days ';

    protected function configure(): void
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription(self::DESCRIPTION);

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $this
            ->getFacade()
            ->generateTimeSlots();

        return self::CODE_SUCCESS;
    }
}
