<?php
/**
 * Durst - project - TransitionLogInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 25.02.21
 * Time: 11:58
 */

namespace Pyz\Zed\Oms\Business\Model;


use DateTime;

interface TransitionLogInterface
{
    /**
     * @param int $idSalesOrder
     * @return \DateTime|null
     */
    public function getDeliveryTimeFromTransitionLogByIdSalesOrder(int $idSalesOrder): ?DateTime;
}
