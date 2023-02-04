<?php
/**
 * Durst - project - DepositManager.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 30.04.18
 * Time: 10:16
 */

namespace Pyz\Zed\Deposit\Business\Model;

use Generated\Shared\Transfer\CartChangeTransfer;
use Generated\Shared\Transfer\DepositTransfer;
use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Pyz\Shared\Deposit\DepositConstants;

class DepositManager implements DepositManagerInterface
{
    public const DEPOSIT_EXPENSE_NAME = 'Pfand';

    /**
     * @var \Pyz\Zed\Deposit\Business\Model\DepositInterface
     */
    protected $depositModel;

    /**
     * DepositManager constructor.
     *
     * @param \Pyz\Zed\Deposit\Business\Model\DepositInterface $depositModel
     */
    public function __construct(DepositInterface $depositModel)
    {
        $this->depositModel = $depositModel;
    }

    /**
     * @param \Generated\Shared\Transfer\CartChangeTransfer $cartChangeTransfer
     *
     * @return \Generated\Shared\Transfer\CartChangeTransfer
     */
    public function addDepositToItems(CartChangeTransfer $cartChangeTransfer): CartChangeTransfer
    {
        foreach ($cartChangeTransfer->getItems() as $itemTransfer) {
            $depositTransfer = $this
                ->depositModel
                ->getDepositForProductBySku($itemTransfer->getSku());

            $itemTransfer->setUnitDeposit($depositTransfer->getDeposit());
            $cartChangeTransfer
                ->getQuote()
                ->addExpense(
                    $this
                        ->createDepositExpense(
                            $depositTransfer,
                            $itemTransfer,
                            $itemTransfer->getSku()
                        )
                );

            $itemTransfer->setDeposit($depositTransfer);
        }

        return $cartChangeTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\DepositTransfer $depositTransfer
     * @param \Generated\Shared\Transfer\ItemTransfer $item
     * @param string $sku
     *
     * @return \Generated\Shared\Transfer\ExpenseTransfer
     */
    protected function createDepositExpense(DepositTransfer $depositTransfer, ItemTransfer $item, string $sku): ExpenseTransfer
    {
        $amount = $depositTransfer->getDeposit();
        $quantity = $item->getQuantity();
        $weight = $depositTransfer->getWeight();

        return (new ExpenseTransfer())
            ->setType(
                sprintf(
                    '%s-%s',
                    DepositConstants::DEPOSIT_EXPENSE_TYPE,
                    $sku
                )
            )
            ->setSumPrice(0)
            ->setUnitGrossPrice($amount)
            ->setSumGrossPrice($amount * $quantity)
            ->setQuantity($quantity)
            ->setWeight($weight)
            ->setSumWeight($quantity * $weight)
            ->setName(DepositConstants::DEPOSIT_EXPENSE_NAME)
            ->setRefundableAmount($amount);
    }
}
