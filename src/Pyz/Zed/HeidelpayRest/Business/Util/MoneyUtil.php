<?php
/**
 * Durst - project - MoneyUtil.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 25.01.19
 * Time: 11:21
 */

namespace Pyz\Zed\HeidelpayRest\Business\Util;

use Generated\Shared\Transfer\OrderTransfer;
use Pyz\Zed\HeidelpayRest\Business\Exception\NegativeAmountException;
use Pyz\Zed\HeidelpayRest\Dependency\Facade\HeidelpayRestToMoneyBridgeInterface;

class MoneyUtil implements MoneyUtilInterface
{
    /**
     * @var \Pyz\Zed\HeidelpayRest\Dependency\Facade\HeidelpayRestToMoneyBridgeInterface
     */
    protected $moneyFacade;

    /**
     * MoneyUtil constructor.
     *
     * @param \Pyz\Zed\HeidelpayRest\Dependency\Facade\HeidelpayRestToMoneyBridgeInterface $moneyFacade
     */
    public function __construct(
        HeidelpayRestToMoneyBridgeInterface $moneyFacade
    ) {
        $this->moneyFacade = $moneyFacade;
    }

    /**
     * {@inheritdoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $order
     * @return float
     */
    public function getDecimalGrandTotalForOrder(OrderTransfer $order): float
    {
        $grandTotal = $this
            ->getGrandTotalForOrder($order);

        return $this
            ->moneyFacade
            ->convertIntegerToDecimal($grandTotal);
    }

    /**
     * {@inheritdoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return int
     */
    public function getGrandTotalForOrder(OrderTransfer $orderTransfer): int
    {
        $orderTransfer
            ->requireTotals()
            ->getTotals()
            ->requireGrandTotal();

        return $orderTransfer
            ->getTotals()
            ->getGrandTotal();
    }

    /**
     * @param int $amount
     *
     * @return float
     */
    public function getFloatFromInt(int $amount): float
    {
        return $this
            ->moneyFacade
            ->convertIntegerToDecimal($amount);
    }

    /**
     * {@inheritDoc}
     *
     * @param float $alreadyCharged
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @return int
     */
    public function getCancelableAmountForOrder(float $alreadyCharged, OrderTransfer $orderTransfer): int
    {
        $grandTotal = $this->getGrandTotalForOrder($orderTransfer);
        $cancelableAmount = ($this->getIntFromFloat($alreadyCharged) - $grandTotal);

        if($cancelableAmount < 0){
            throw NegativeAmountException::cancelable($cancelableAmount, $orderTransfer->getIdSalesOrder());
        }

        return $cancelableAmount;
    }

    /**
     * @param float $amount
     *
     * @return int
     */
    public function getIntFromFloat(float $amount): int
    {
        return $this
            ->moneyFacade
            ->convertDecimalToInteger($amount);
    }
}
