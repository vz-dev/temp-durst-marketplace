<?php
/**
 * Durst - project - TaxProductConnectorBusinessFactory.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 25.06.20
 * Time: 19:41
 */

namespace Pyz\Zed\TaxProductConnector\Business;

use Pyz\Zed\TaxProductConnector\Business\Model\ProductItemTaxRateCalculator;
use Spryker\Zed\TaxProductConnector\Business\TaxProductConnectorBusinessFactory as SprykerTaxProductConnectorBusinessFactory;

class TaxProductConnectorBusinessFactory extends SprykerTaxProductConnectorBusinessFactory
{
    /**
     * @return \Pyz\Zed\TaxProductConnector\Business\Model\ProductItemTaxRateCalculator|\Spryker\Zed\TaxProductConnector\Business\Model\ProductItemTaxRateCalculator
     */
    public function createProductItemTaxRateCalculator()
    {
        return new ProductItemTaxRateCalculator(
            $this->getQueryContainer(),
            $this->getTaxFacade()
        );
    }
}
