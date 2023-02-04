<?php
/**
 * Durst - project - RefundSaver.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 08.01.20
 * Time: 16:23
 */

namespace Pyz\Zed\Refund\Business\Model;

use Exception;
use Generated\Shared\Transfer\RefundTransfer;
use Spryker\Zed\Refund\Business\Model\RefundSaver as SprykerRefundSaver;

class RefundSaver extends SprykerRefundSaver
{
    /**
     * @param \Generated\Shared\Transfer\RefundTransfer $refundTransfer
     * @return bool
     * @throws \Exception
     */
    public function saveRefund(RefundTransfer $refundTransfer)
    {
        $this
            ->salesQueryContainer
            ->getConnection()
            ->beginTransaction();

        try {
            $this
                ->updateOrderItems($refundTransfer);
            $this
                ->updateExpenses($refundTransfer);
            $this
                ->storeRefund($refundTransfer);
            $this
                ->recalculateOrder($refundTransfer);

        } catch (Exception $exception) {
            $this
                ->salesQueryContainer
                ->getConnection()
                ->rollBack();

            throw $exception;
        }

        return $this
            ->salesQueryContainer
            ->getConnection()
            ->commit();
    }
}
