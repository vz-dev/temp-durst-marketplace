<?php
/**
 * Durst - project - AbstractCsvExportManager.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 18.04.22
 * Time: 10:52
 */

namespace Pyz\Zed\Billing\Business\Model\File;

use DateTime;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Propel\Runtime\Exception\PropelException;

class AbstractCsvExportManager
{
    /**
     * @param SpySalesOrder $order
     * @return string
     * @throws PropelException
     */
    protected function getTourReference(SpySalesOrder $order) : string
    {
        if($order->getSpyConcreteTimeSlot() !== null)
        {
            return $order->getSpyConcreteTimeSlot()->getDstConcreteTour()->getTourReference();
        }

        $gmOrder = $this
            ->graphmastersFacade
            ->getOrderByReference($order->getOrderReference());

        return $gmOrder->getTourReference();
    }

    /**
     * @param SpySalesOrder $order
     * @return DateTime
     * @throws PropelException
     */
    protected function getStartTime(SpySalesOrder $order) : DateTime
    {
        if($order->getSpyConcreteTimeSlot() !== null)
        {
            return $order->getSpyConcreteTimeSlot()->getStartTime();
        }

        return $order->getGmStartTime();
    }

    /**
     * @param SpySalesOrder $order
     * @return DateTime
     * @throws PropelException
     */
    protected function getEndTime(SpySalesOrder $order) : DateTime
    {
        if($order->getSpyConcreteTimeSlot() !== null)
        {
            return $order->getSpyConcreteTimeSlot()->getEndTime();
        }

        return $order->getGmEndTime();
    }
}
