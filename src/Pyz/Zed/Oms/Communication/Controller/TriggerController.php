<?php

namespace Pyz\Zed\Oms\Communication\Controller;

use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Pyz\Zed\Oms\Business\OmsFacadeInterface;
use Pyz\Zed\Oms\Communication\OmsCommunicationFactory;
use Pyz\Zed\Oms\Persistence\OmsQueryContainerInterface;
use Spryker\Zed\Oms\Communication\Controller\TriggerController as SprykerTriggerController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method OmsFacadeInterface getFacade()
 * @method OmsCommunicationFactory getFactory()
 * @method OmsQueryContainerInterface getQueryContainer()
 */
class TriggerController extends SprykerTriggerController
{
    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function triggerEventForOrderItemsAction(Request $request): RedirectResponse
    {
        $idOrderItem = $this->castId($request->query->getInt('id-sales-order-item'));
        $event = $request->query->get('event');
        $redirect = $request->query->get('redirect', '/');

        $this->getFacade()->triggerEventForOrderItems($event, [$idOrderItem]);

        $this
            ->getFactory()
            ->getSalesFacade()
            ->setOrderItemsStuck([$idOrderItem], false);

        $this->addInfoMessage('Status change triggered successfully.');

        return $this->redirectResponse($redirect);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function triggerEventForOrderAction(Request $request): RedirectResponse
    {
        $idOrder = $this->castId($request->query->getInt('id-sales-order'));
        $event = $request->query->get('event');
        $redirect = $request->query->get('redirect', '/');
        $itemsList = $request->query->get('items');

        $orderItems = $this->getOrderItemsToTriggerAction($idOrder, $itemsList);

        $this->getFacade()->triggerEvent($event, $orderItems, []);

        $orderItemIds = array_map(function (SpySalesOrderItem $orderItem) {
            return $orderItem->getIdSalesOrderItem();
        }, $orderItems->getArrayCopy());

        $this
            ->getFactory()
            ->getSalesFacade()
            ->setOrderItemsStuck($orderItemIds, false);

        $this->addInfoMessage('Status change triggered successfully.');

        return $this->redirectResponse($redirect);
    }
}
