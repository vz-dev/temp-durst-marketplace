<?php
/**
 * Durst - project - BatchProductExportController.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 01.10.20
 * Time: 09:09
 */

namespace Pyz\Zed\ProductExport\Communication\Controller\Console;


use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BatchProductExportController
 * @package Pyz\Zed\ProductExport\Communication\Controller\Console
 * @method \Pyz\Zed\ProductExport\Business\ProductExportFacadeInterface getFacade()
 */
class BatchProductExportConsole extends Console
{
    public const COMMAND_NAME = 'product:export:batch';
    protected const COMMAND_DESCRIPTION = 'Creates a CSV file for all active products and the merchant price with its state.';

    /**
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
                    'p:e:b'
                ]
            );

        parent::configure();
    }

    /**
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
            ->exportNext();
    }
}
