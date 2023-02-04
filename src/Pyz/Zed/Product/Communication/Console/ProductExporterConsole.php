<?php
/**
 * Durst - project - ProductExporterConsole.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 14.09.18
 * Time: 11:40
 */

namespace Pyz\Zed\Product\Communication\Console;


use Pyz\Shared\Product\ProductConstants;
use Pyz\Zed\Product\Communication\Plugin\ProductExporter\ProductExporterPlugin;
use Spryker\Shared\Config\Config;
use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class ProductExporterConsole extends Console
{
    const COMMAND_NAME = 'product:export';
    const COMMAND_DESCRIPTION = 'Creates a csv export of all products';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription(self::COMMAND_DESCRIPTION);

        parent::configure();
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->info(self::COMMAND_DESCRIPTION);

        $command = 'APPLICATION_ENV=' . APPLICATION_ENV
            . ' APPLICATION_STORE=' . APPLICATION_STORE
            . ' APPLICATION_ROOT_DIR=' . APPLICATION_ROOT_DIR
            . ' APPLICATION=' . APPLICATION
            . ' vendor/bin/console middleware:process:run -p '.ProductExporterPlugin::PROCESS_NAME
            . ' -o ' . Config::get(ProductConstants::PRODUCT_EXPORTER_PATH);

        $process = new Process(
            $command,
            APPLICATION_ROOT_DIR,
            null,
            null,
            6000
        );

        return $process->run(function ($type, $buffer) {
            echo $buffer;
        });
    }
}