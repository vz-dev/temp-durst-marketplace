<?php
/**
 * Durst - project - GraphMastersTourFixCutOffReached.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 22.03.22
 * Time: 07:58
 */

namespace Pyz\Zed\GraphMasters\Communication\Console;

use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\GraphMasters\Business\GraphMastersFacadeInterface;
use Spryker\Zed\Kernel\Communication\Console\Console;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;
use Spryker\Zed\Sales\Business\Exception\InvalidSalesOrderException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method GraphMastersFacadeInterface getFacade()
 */
class GraphMastersTourFixCutOffReachedConsole  extends Console
{
    public const COMMAND_NAME = 'graphmasters:tours:fix:cutoff-reached';
    public const DESCRIPTION = 'Fixes all Graphmasters tours which have reached their cutoff time and have not exported goods via edi';

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
     * @return int|null
     * @throws PropelException
     * @throws ContainerKeyNotFoundException
     * @throws AmbiguousComparisonException
     * @throws InvalidSalesOrderException
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $this
            ->getFacade()
            ->fixOpenToursCutoffReached();

        return self::CODE_SUCCESS;
    }
}
