<?php
/**
 * Durst - project - IndexController.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 01.09.21
 * Time: 15:25
 */

namespace Pyz\Zed\CancelOrder\Communication\Controller;

use Exception;
use Generated\Shared\Transfer\OrderTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Pyz\Zed\CancelOrder\Business\CancelOrderFacadeInterface;
use Pyz\Zed\CancelOrder\Communication\CancelOrderCommunicationFactory;
use Pyz\Zed\CancelOrder\Communication\Plugin\OMS\Command\StartCancel;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class IndexController
 * @package Pyz\Zed\CancelOrder\Communication\Controller
 *
 * @method CancelOrderCommunicationFactory getFactory()
 * @method CancelOrderFacadeInterface getFacade()
 */
class IndexController extends AbstractController
{
    protected const URL_PARAMETER_TOKEN = 't';
    protected const URL_PARAMETER_REDIRECT = 'redirect';

    protected const SUCCESS_CANCEL_ORDER_MESSAGE = 'Die Bestellung "%s" wurde erfolgreich storniert.';

    /**
     * @return array
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function indexAction(): array
    {
        $table = $this
            ->getFactory()
            ->createCancelOrderTable();

        return $this
            ->viewResponse(
                [
                    'table' => $table->render()
                ]
            );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function tableAction(): JsonResponse
    {
        $table = $this
            ->getFactory()
            ->createCancelOrderTable();

        return $this
            ->jsonResponse(
                $table
                    ->fetchData()
            );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function cancelAction(Request $request): RedirectResponse
    {
        $token = $request
            ->get(
                static::URL_PARAMETER_TOKEN
            );
        $redirectUrl = $request
            ->get(
                static::URL_PARAMETER_REDIRECT,
                '/'
            );

        try {
            // create JWT from token
            $transfer = $this
                ->getFacade()
                ->prepareTriggerFromToken(
                    $token
                );

            // find order by ID from token
            $order = $this
                ->getFactory()
                ->getSalesFacade()
                ->getOrderByIdSalesOrder(
                    $transfer
                        ->getId()
                );

            // get all IDs from the sales order items in this order
            $idSalesOrderItems = $this
                ->getSalesOrderItemIds(
                    $order
                );

            $this
                ->getFactory()
                ->getOmsFacade()
                ->triggerEventForOrderItems(
                    StartCancel::EVENT_ID,
                    $idSalesOrderItems
                );

            $this
                ->addSuccessMessage(
                    sprintf(
                        static::SUCCESS_CANCEL_ORDER_MESSAGE,
                        $transfer
                            ->getSubject()
                    )
                );
        } catch (Exception $exception) {
            $this
                ->addErrorMessage(
                    $exception
                        ->getMessage()
                );
        }

        return $this
            ->redirectResponse(
                $redirectUrl
            );
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $order
     * @return array
     */
    protected function getSalesOrderItemIds(
        OrderTransfer $order
    ): array
    {
        $ids = [];

        foreach ($order->getItems() as $item) {
            $ids[] = $item
                ->getIdSalesOrderItem();
        }

        return $ids;
    }
}
