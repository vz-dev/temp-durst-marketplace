<?php

namespace Pyz\Zed\Product\Communication\Console;


use Pyz\Shared\Product\ProductConstants;
use Pyz\Zed\Product\Communication\Plugin\ProductExporter\ProductExporterPlugin;
use Spryker\Shared\Config\Config;
use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * Class ProductDeactivationConsole
 * @package Pyz\Zed\Product\Communication\Console
 * @method \Pyz\Zed\Product\Business\ProductFacadeInterface getFacade()
 * @method \Pyz\Zed\MerchantPrice\Business\MerchantPriceFacadeInterface getPriceFacade()
 */
class ProductDeactivationConsole extends Console
{
    const COMMAND_NAME = 'product:deactivate';
    const COMMAND_DESCRIPTION = 'Deactivates a list of products by sku';

    const ARGUMENT_SKU = 'sku';
    const ARGUMENT_SKU_DESCRIPTION = 'Product Skus to be deactivated separated by comma.';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription(self::COMMAND_DESCRIPTION);
        $this->addArgument(
                self::ARGUMENT_SKU,
                InputArgument::REQUIRED,
                self::ARGUMENT_SKU_DESCRIPTION
            );

        parent::configure();
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
      $sku = $input->getArgument(self::ARGUMENT_SKU);
      $skuArray = explode (",", $sku);

      foreach ($skuArray as $sku) {
          $idProduct = $this->getFacade()->findProductConcreteIdBySku($sku);
          $idBranches = $this->getPriceFacade()->getIdBranchForActivePrice($sku);
          $this->getFacade()->deactivateProductConcrete($idProduct);
      }

      print_r($idBranches);
    }
}
