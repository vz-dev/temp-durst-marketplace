<?php
/**
 * Durst - project - ItemsHydrate.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 19.11.20
 * Time: 15:01
 */

namespace Pyz\Zed\Integra\Business\Model\Quote;

use Generated\Shared\Transfer\ItemTransfer;
use Pyz\Zed\Integra\Business\Model\Quote\Deposit\DepositRepositoryInterface;
use Spryker\Zed\Money\Business\MoneyFacadeInterface;

class ItemsHydrator implements ItemsHydratorInterface
{
    protected const MERCHANT_SKU_W_TYPE_FORMAT = '%s_%s';

    /**
     * @var MoneyFacadeInterface
     */
    protected $moneyFacade;

    /**
     * @var DepositRepositoryInterface
     */
    protected $depositRepo;

    /**
     * ItemsHydrator constructor.
     * @param MoneyFacadeInterface $moneyFacade
     * @param DepositRepositoryInterface $depositRepo
     */
    public function __construct(
        MoneyFacadeInterface $moneyFacade,
        DepositRepositoryInterface $depositRepo
    )
    {
        $this->moneyFacade = $moneyFacade;
        $this->depositRepo = $depositRepo;
    }

    /**
     * @param string $positionDid
     * @param array $item
     * @param string $sku
     *
     * @return ItemTransfer
     */
    public function createItem(string $positionDid, array $item, string $sku): ItemTransfer
    {
        $taxAmount = $this->toInt($item['tax_amount']) / $item['quantity'];
        $price = $this->toInt($item['amount']) / $item['quantity'];
        $netPrice = $this->toInt($item['net_amount']) / $item['quantity'];
        $depositAmount = $this->getDepositAmount($item);

        return (new ItemTransfer())
            ->setSku($sku)
            ->setName($item['name'])
            ->setSumDeposit($depositAmount)
            ->setConcreteAttributes([])
            ->setSumDepositTaxAmount(0)
            ->setUnitDeposit($depositAmount)
            ->setUnitDepositTaxAmount(0)
            ->setCanceledAmount(0)
            ->setSumSubtotalAggregation(0)
            ->setUnitSubtotalAggregation(0)
            ->setSumTaxAmountFullAggregation(0)
            ->setUnitTaxAmountFullAggregation(0)
            ->setTaxRateAverageAggregation(0.0)
            ->setTaxAmountAfterCancellation(0)
            ->setSumExpensePriceAggregation(0)
            ->setUnitExpensePriceAggregation(0)
            ->setSumDiscountAmountAggregation(0)
            ->setUnitDiscountAmountAggregation(0)
            ->setSumDiscountAmountFullAggregation(0)
            ->setUnitDiscountAmountFullAggregation(0)
            ->setSumPriceToPayAggregation($price)
            ->setRefundableAmount($price)
            ->setUnitGrossPrice($price)
            ->setSumGrossPrice($price)
            ->setSumNetPrice($netPrice)
            ->setUnitNetPrice($netPrice)
            ->setMerchantSku($this->appendUnitTypeToMerchantSku($item['merchant_sku'], $item['unit_type']))
            ->setIntegraPositionDid($positionDid)
            ->setQuantity(1)
            ->setTaxRate($item['tax_rate'])
            ->setUnitTaxAmount($taxAmount)
            ->setUnitPrice($price)
            ->setSumTaxAmount($taxAmount)
            ->setSumPrice($price);
    }

    /**
     * @param float $value
     *
     * @return int
     */
    protected function toInt(float $value): int
    {
        return $this
            ->moneyFacade
            ->convertDecimalToInteger($value);
    }

    /**
     * @param array $itemData
     * @return int
     */
    protected function getDepositAmount(array $itemData) : int
    {
        if(array_key_exists('amount', $itemData['deposit']) !== true){
            return 0;
        }
        return $this->moneyFacade->convertDecimalToInteger($itemData['deposit']['amount']) / $itemData['deposit']['quantity'];
    }


    /**
     * @param string $merchant_sku
     * @param string $unit_type
     * @return string
     */
    protected function appendUnitTypeToMerchantSku(string $merchant_sku, string $unit_type) : string
    {
        if($unit_type !== 'KA')
        {
            return sprintf(static::MERCHANT_SKU_W_TYPE_FORMAT, $merchant_sku, $unit_type);
        }

        return $merchant_sku;
    }
}
