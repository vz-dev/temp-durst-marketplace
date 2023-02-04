<?php
/**
 * Durst - project - BatchPriceImportConsole.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 07.10.20
 * Time: 14:28
 */

namespace Pyz\Zed\PriceImport\Communication\Controller\Console;


use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BatchPriceImportConsole
 * @package Pyz\Zed\PriceImport\Communication\Controller\Console
 * @method \Pyz\Zed\PriceImport\Business\PriceImportFacadeInterface getFacade()
 */
class BatchPriceImportConsole extends Console
{
    public const COMMAND_NAME = 'price:import:batch';
    protected const COMMAND_DESCRIPTION = 'Reads a CSV file and deletes, inserts or updates all branch specific prices.';

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName(
                static::COMMAND_NAME
            );

        $this
            ->setDescription(
                static::COMMAND_DESCRIPTION
            );

        $this
            ->setAliases(
                [
                    'p:i:b'
                ]
            );

        parent::configure();
    }

    /**
     * {@inheritDoc}
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return void
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): void
    {
        $this
            ->info(
                static::COMMAND_DESCRIPTION
            );

        $this
            ->getFacade()
            ->importNext();
    }
}
