<?php
/**
 * Durst - project - ProductRelevanceConsole.php.
 *
 * Initial version by:
 * User: Zhaklina Basha, <zhaklina.basha@durst.shop>
 * Date: 16.08.21
 * Time: 12:47
 */

namespace Pyz\Zed\MerchantPrice\Communication\Console;

use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ProductRelevanceConsole
 * @package Pyz\Zed\MerchantPrice\Communication\Console
 * @method \Pyz\Zed\MerchantPrice\Business\MerchantPriceFacadeInterface getFacade()
 */
class ProductRelevanceConsole extends Console
{
    public const COMMAND_NAME = 'product-relevance:update:count-sold-items';
    public const DESCRIPTION = 'This command will count the sold items for each branch in the last month';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription(self::DESCRIPTION);
        $this->setAliases(['generate:product-relevance', 'da:c:cts']);

        parent::configure();
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this
            ->getFacade()
            ->createCountItems();
    }
}
