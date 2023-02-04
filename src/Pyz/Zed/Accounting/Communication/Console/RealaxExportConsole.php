<?php
/**
 * Durst - project - RealaxExportConsole.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 31.03.20
 * Time: 09:32
 */

namespace Pyz\Zed\Accounting\Communication\Console;


use Pyz\Zed\Accounting\Business\AccountingFacadeInterface;
use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RealaxExportConsole
 * @package Pyz\Zed\Accounting\Communication\Console
 * @method AccountingFacadeInterface getFacade()
 */
class RealaxExportConsole extends Console
{
    public const COMMAND_NAME = 'realax:invoice:export:variable';
    protected const COMMAND_DESCRIPTION = 'Creates a CSV file in the Realax format with all variable values for the given merchant or for all merchants to the (optional) given path.';

    public const OPTION_MERCHANT_ID_NAME = 'idMerchant';
    protected const OPTION_MERCHANT_ID_DESCRIPTION = 'Id of the merchant to export';

    public const OPTION_GENERATE_PATH_NAME = 'path';
    protected const OPTION_GENERATE_PATH_DESCRIPTION = 'Path where the export should be saved.';

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName(static::COMMAND_NAME);
        $this
            ->setDescription(static::COMMAND_DESCRIPTION);
        $this
            ->addArgument(
                static::OPTION_MERCHANT_ID_NAME,
                InputArgument::OPTIONAL,
                static::OPTION_MERCHANT_ID_DESCRIPTION
            );
        $this
            ->addArgument(
                static::OPTION_GENERATE_PATH_NAME,
                InputArgument::OPTIONAL,
                static::OPTION_GENERATE_PATH_DESCRIPTION
            );
        $this
            ->setAliases(
                [
                    'r:i:e:v'
                ]
            );

        parent::configure();
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this
            ->info(
                static::COMMAND_DESCRIPTION
            );

        $idMerchants = $input
            ->getArgument(
                static::OPTION_MERCHANT_ID_NAME
            );

        if (is_string($idMerchants) === true) {
            $idMerchants = [
                (int)$idMerchants
            ];
        }

        if ($idMerchants === null) {
            $idMerchants = $this
                ->getFacade()
                ->getAllMerchantsForRealaxExport();
        }

        $csvPath = $input
            ->getArgument(
                static::OPTION_GENERATE_PATH_NAME
            );

        foreach ($idMerchants as $idMerchant) {
            $this
                ->getFacade()
                ->exportRealax(
                    $idMerchant,
                    $csvPath
                );
        }
    }
}
