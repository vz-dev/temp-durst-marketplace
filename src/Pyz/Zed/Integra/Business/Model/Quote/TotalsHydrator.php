<?php
/**
 * Durst - project - TotalsHydrator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 19.11.20
 * Time: 15:02
 */

namespace Pyz\Zed\Integra\Business\Model\Quote;


use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\TaxTotalTransfer;
use Generated\Shared\Transfer\TotalsTransfer;

class TotalsHydrator implements TotalsHydratorInterface
{
    /**
     * @param QuoteTransfer $quoteTransfer
     *
     * @return TotalsTransfer
     */
    public function createTotalsTransfer(QuoteTransfer $quoteTransfer): TotalsTransfer
    {
        $subTotal = 0;
        $taxTotal = 0;
        $depositTotal = $this->getDepositTotal($quoteTransfer);

        foreach ($quoteTransfer->getItems() as $item) {
            $subTotal += $item->getSumPrice();
            $taxTotal += $item->getSumTaxAmount();
        }
        return (new TotalsTransfer())
            ->setTaxTotal((new TaxTotalTransfer())->setTaxRate(19.0)->setAmount($taxTotal))
            ->setGrandTotal($subTotal+$depositTotal)
            ->setDepositTotal($depositTotal)
            ->setDisplayTotal(0)
            ->setGrossSubtotal(0)
            ->setWeightTotal(0)
            ->setDeliveryCostTotal(0)
            ->setSubtotal($subTotal)
            ->setExpenseTotal(0)
            ->setDiscountTotal(0)
            ->setCanceledTotal(0);
    }

    /**
     * @param QuoteTransfer $quoteTransfer
     * @return int
     */
    protected function getDepositTotal(QuoteTransfer $quoteTransfer) : int
    {
        $depositTotal = 0;
        foreach ($quoteTransfer->getExpenses() as $expense) {
            $depositTotal += $expense->getSumGrossPrice();
        }

        return $depositTotal;
    }
}
