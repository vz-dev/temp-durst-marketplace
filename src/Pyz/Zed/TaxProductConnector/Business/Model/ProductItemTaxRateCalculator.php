<?php
/**
 * Durst - project - ProductItemTaxRateCalculator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 25.06.20
 * Time: 19:35
 */

namespace Pyz\Zed\TaxProductConnector\Business\Model;

use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\TaxProductConnector\Business\Model\ProductItemTaxRateCalculator as SprykerProductItemTaxRateCalculator;

/**
 * Class ProductItemTaxRateCalculator
 * @package Pyz\Zed\TaxProductConnector\Business\Model
 * @property \Pyz\Zed\TaxProductConnector\Persistence\TaxProductConnectorQueryContainerInterface  $taxQueryContainer
 */
class ProductItemTaxRateCalculator extends SprykerProductItemTaxRateCalculator
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return void
     */
    public function recalculate(QuoteTransfer $quoteTransfer)
    {
        $countryIso2Code = $this->getShippingCountryIso2Code($quoteTransfer);
        $allIdProductAbstracts = $this->getAllIdAbstractProducts($quoteTransfer);

        if (!$countryIso2Code) {
            $countryIso2Code = $this->taxFacade->getDefaultTaxCountryIso2Code();
        }

        $date = new \DateTime('now');
        if($quoteTransfer->getConcreteTimeSlots()->count() === 1){
            /** @var \Generated\Shared\Transfer\ConcreteTimeSlotTransfer $concreteTimeSlot */
            $concreteTimeSlot = $quoteTransfer->getConcreteTimeSlots()->offsetGet(0);
            $date = new \DateTime($concreteTimeSlot->getStartTime());
        }
        $taxRates = $this->findTaxRatesByAllIdProductAbstractsAndCountryIso2CodeForDate($allIdProductAbstracts, $countryIso2Code, $date);
        $this->setItemsTax($quoteTransfer, $taxRates);
    }

    /**
     * @param array $allIdProductAbstracts
     * @param $countryIso2Code
     * @param \DateTime $date
     * @return array
     */
    protected function findTaxRatesByAllIdProductAbstractsAndCountryIso2CodeForDate(array $allIdProductAbstracts, $countryIso2Code, \DateTime $date)
    {
        return $this
            ->taxQueryContainer
            ->queryTaxSetByIdProductAbstractAndCountryIso2CodeForDate($allIdProductAbstracts, $countryIso2Code, $date)
            ->find()
            ->toArray();
    }
}
