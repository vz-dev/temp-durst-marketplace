<?php
/**
 * Durst - project - ExpensesHydrator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 19.11.20
 * Time: 15:01
 */

namespace Pyz\Zed\Integra\Business\Model\Quote;

use Generated\Shared\Transfer\ExpenseTransfer;
use Pyz\Zed\Integra\Business\Model\Quote\Deposit\DepositRepositoryInterface;
use Pyz\Zed\Tax\Business\TaxFacadeInterface;
use Spryker\Zed\Money\Business\MoneyFacadeInterface;

class ExpensesHydrator implements ExpensesHydratorInterface
{
    /**
     * @var DepositRepositoryInterface
     */
    protected $depositRepo;

    /**
     * @var TaxFacadeInterface
     */
    protected $taxFacade;

    /**
     * @var MoneyFacadeInterface
     */
    protected $moneyFacade;

    /**
     * ExpensesHydrator constructor.
     *
     * @param DepositRepositoryInterface $depositRepo
     * @param TaxFacadeInterface $taxFacade
     * @param MoneyFacadeInterface $moneyFacade
     */
    public function __construct(
        DepositRepositoryInterface $depositRepo,
        TaxFacadeInterface $taxFacade,
        MoneyFacadeInterface $moneyFacade
    ) {
        $this->depositRepo = $depositRepo;
        $this->taxFacade = $taxFacade;
        $this->moneyFacade = $moneyFacade;
    }

    /**
     * @param int $idBranch
     * @param array $itemData
     * @param string $sku
     * @param int $count
     * @return ExpenseTransfer
     */
    public function createExpense(
        int $idBranch,
        array $itemData,
        string $sku,
        int $count
    ): ExpenseTransfer {
        $depositGrossAmount = $this->getDepositValuesByKey('amount', $itemData);
        $depositNetAmount = $this->getDepositValuesByKey('net_amount', $itemData);
        $taxAmount = $this->getTaxRate($itemData);
        $taxRate = $this->getDepositValuesByKey('tax_rate', $itemData);

        $type = sprintf('deposit-%s-%d', $sku, $count);

        return (new ExpenseTransfer())
            ->setType($type)
            ->setMerchantSku($sku)
            ->setName('Pfand')
            ->setTaxRate($taxRate)
            ->setIsNegative(false)
            ->setQuantity(1)
            ->setUnitGrossPrice($depositGrossAmount)
            ->setUnitNetPrice($depositNetAmount)
            ->setSumGrossPrice($depositGrossAmount)
            ->setSumNetPrice($depositNetAmount)
            ->setSumTaxAmount($taxAmount)
            ->setUnitTaxAmount($taxAmount)
            ->setUnitPrice($depositGrossAmount)
            ->setUnitPriceToPayAggregation($depositGrossAmount)
            ->setRefundableAmount($depositGrossAmount);
    }

    /**
     * @param string $valueKey
     * @param array $itemData
     * @return int
     */
    protected function getDepositValuesByKey(string $valueKey, array $itemData) : int
    {
        if(array_key_exists($valueKey, $itemData['deposit']) !== true){
            return 0;
        }

        return $this->moneyFacade->convertDecimalToInteger($itemData['deposit'][$valueKey]) / $itemData['deposit']['quantity'];
    }

    /**
     * @param string $valueKey
     * @param array $itemData
     * @return int
     */
    protected function getTaxRate(array $itemData) : int
    {
        if(array_key_exists('tax_rate', $itemData['deposit']) !== true){
            return 0;
        }

        return $this->moneyFacade->convertDecimalToInteger($itemData['deposit']['tax_rate']);
    }
}
