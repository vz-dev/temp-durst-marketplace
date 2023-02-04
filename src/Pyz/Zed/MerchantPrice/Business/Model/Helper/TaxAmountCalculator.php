<?php
/**
 * Durst - project - TaxAmountCalculator.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 20.07.18
 * Time: 10:49
 */

namespace Pyz\Zed\MerchantPrice\Business\Model\Helper;


use Generated\Shared\Transfer\TaxRateTransfer;
use Orm\Zed\MerchantPrice\Persistence\MerchantPrice;
use Pyz\Zed\MerchantPrice\MerchantPriceConfig;
use Spryker\Zed\Tax\Business\TaxFacadeInterface;

class TaxAmountCalculator implements TaxAmountCalculatorInterface
{
    /**
     * @var TaxFacadeInterface
     */
    protected $taxFacade;

    /**
     * @var MerchantPriceConfig
     */
    protected $config;

    /**
     * TaxAmountCalculator constructor.
     * @param TaxFacadeInterface $taxFacade
     * @param MerchantPriceConfig $config
     */
    public function __construct(TaxFacadeInterface $taxFacade, MerchantPriceConfig $config)
    {
        $this->taxFacade = $taxFacade;
        $this->config = $config;
    }

    /**
     * @param int $idTaxSet
     * @return float
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function getTaxRate(int $idTaxSet) : float
    {
        $taxSet = $this
            ->taxFacade
            ->getTaxSet($idTaxSet);
        
        foreach ($taxSet->getTaxRates() as $taxRate) {

            if(
                $taxRate->getCountry()->getIso3Code() === $this->config->getDefaultCountryIsoCode() &&
                $this->checkTaxRateValid($taxRate)
            ){
                return $taxRate->getRate();
            }
        }

        return $this
            ->taxFacade
            ->getDefaultTaxRate();
    }

    /**
     * @param MerchantPrice $entity
     * @param float $taxRate
     * @return int
     */
    protected function getTaxAmountFromGrossPrice(MerchantPrice $entity, float $taxRate) : int
    {
        $this
            ->taxFacade
            ->resetAccruedTaxCalculatorRoundingErrorDelta();

        return $this
            ->taxFacade
            ->getAccruedTaxAmountFromGrossPrice($entity->getGrossPrice(), $taxRate);
    }

    /**
     * @param MerchantPrice $entity
     * @param float $taxRate
     * @return int
     */
    protected function getTaxAmountFromNetPrice(MerchantPrice $entity, float $taxRate) : int
    {
        $this
            ->taxFacade
            ->resetAccruedTaxCalculatorRoundingErrorDelta();

        return $this
            ->taxFacade
            ->getAccruedTaxAmountFromNetPrice($entity->getPrice(), $taxRate);
    }

    /**
     * @param MerchantPrice $entity
     * @return int
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function getIdTaxSetFromPriceEntity(MerchantPrice $entity) : int
    {
        return $entity->getSpyProduct()->getSpyProductAbstract()->getFkTaxSet();
    }

    /**
     * @param MerchantPrice $entity
     * @return int
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function calculateGrossPrice(MerchantPrice $entity) : int
    {
        $taxRate = $this->getTaxRate($this->getIdTaxSetFromPriceEntity($entity));

        return $entity->getPrice() + $this->getTaxAmountFromNetPrice($entity, $taxRate);
    }

    /**
     * @param MerchantPrice $entity
     * @return int
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function calculateNetPrice(MerchantPrice $entity) : int
    {
        $taxRate = $this->getTaxRate($this->getIdTaxSetFromPriceEntity($entity));

        return $entity->getGrossPrice() - $this->getTaxAmountFromGrossPrice($entity, $taxRate);
    }

    /**
     * @param TaxRateTransfer $taxRateTransfer
     * @return bool
     */
    protected function checkTaxRateValid(TaxRateTransfer $taxRateTransfer) :  bool
    {
        $today = date('Y-m-d');

        if ($taxRateTransfer->getValidFrom() !== null && $today < $taxRateTransfer->getValidFrom())
        {
            return false;
        }

        if ($taxRateTransfer->getValidTo() !== null && $today > $taxRateTransfer->getValidTo())
        {
            return false;
        }

        return true;
    }
}
