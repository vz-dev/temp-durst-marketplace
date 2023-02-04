<?php
/**
 * Durst - project - TestController.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 31.08.21
 * Time: 09:15
 */

namespace Pyz\Zed\CancelOrder\Communication\Controller;

use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Orm\Zed\Sales\Persistence\SpySalesOrderQuery;
use Pyz\Shared\CancelOrder\CancelOrderConstants;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder\MarkCancel;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;

/**
 * @method \Pyz\Zed\CancelOrder\Business\CancelOrderFacadeInterface getFacade()
 * @method \Pyz\Zed\CancelOrder\Communication\CancelOrderCommunicationFactory getFactory()
 */
class TestController extends AbstractController
{
    public function testAction()
    {
//        $jwt = $this
//            ->getFacade()
//            ->generateTokenForIssuer(
//                10,
//                $this->getFactory()->getConfig()->getIssuerFridge()
//            );
//
//        dump($jwt);

//        $parsed = $this
//            ->getFacade()
//            ->getJwtFromToken(
//                $jwt->getToken()
//            );
//        dump($parsed);

//        $this
//            ->getFacade()
//            ->saveCancelOrder(
//                $jwt->getToken()
//            );

//        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpc3MiOiJmcmlkZ2UiLCJhdWQiOiJPbGl2ZXJoYXREdXJzdCBBRyIsImp0aSI6IjEwIiwiaWF0IjoxNTkzNTI5MTY5LCJuYmYiOjE1OTM1MjkxNjksImV4cCI6MTU5MzYxNTYwMCwic3ViIjoiREUtLTEwIiwiaWRCaWxsaW5nIjoxOSwiaWRTaGlwcGluZyI6MjAsImlkQ29uY3JldGVUb3VyIjoxN30.OK8FeVRXtH7_IKfA2IV1QWNYNoWiKiEMJ-65TxDlhTMyGDxbzxGPvhJdIfzMq0k08obrMnAUYW3_p5FOZ5p-7A';
//        dump($this->getFacade()->getJwtFromToken($token));

//        $transfer = $this
//            ->getFacade()
//            ->getCancelOrderById(
//                1
//            );
//        dump($transfer);

        $token = $this
            ->getFacade()
            ->getTokenForCustomerMail(
                240
            );
        dump($token);
        dump($this->getFacade()->getJwtFromToken($token));

        $token2 = $this
            ->getFacade()
            ->getJwtTransferForFridge(
                240
            );
        dump($token2);

        $token3 = $this
            ->getFacade()
            ->getJwtTransferForDriver(
                240
            );
        dump($token3);

//        $myToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpc3MiOiJjdXN0b21lciIsImF1ZCI6Ik9saXZlcmhhdER1cnN0IEFHIiwianRpIjoiMjQxIiwiaWF0IjoxNjMwNDkxNzY4LCJuYmYiOjE2MzA0OTE3NjgsImV4cCI6MTYzMTAyNjgwMCwic3ViIjoiREUtLTIzNCIsImlkQmlsbGluZyI6NDgxLCJpZFNoaXBwaW5nIjo0ODIsImlkQ29uY3JldGVUb3VyIjo2NzF9.sSVDwfLukLe9f6EIYNdOCS3WMafUOFONREjJbRWzxXpmphSWLxhlD74IjK-qboyybA4d1N_YRcABoNwKx3xWLg';
//        dump($this->getFacade()->getJwtFromToken($myToken));
//
//        $myToken2 = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpc3MiOiJmcmlkZ2UiLCJhdWQiOiJPbGl2ZXJoYXREdXJzdCBBRyIsImp0aSI6IjI0MCIsImlhdCI6MTYzMDQ5MDc4NSwibmJmIjoxNjMwNDkwNzg1LCJleHAiOjE2MzEwMjY4MDAsInN1YiI6IkRFLS0yMzMiLCJpZEJpbGxpbmciOjQ3OSwiaWRTaGlwcGluZyI6NDgwLCJpZENvbmNyZXRlVG91ciI6NjcxfQ.426MWBMl_gKKw2OAdiIPnf5oBRRPowv7-exvrPOJa44nOzrZpsEb-zBd1Qg8M1vfLQUx5rgUu_yxQp8ppUmzwQ';
//        dump($this->getFacade()->getJwtFromToken($myToken2));

        die;
    }

    public function test2Action()
    {
        $order = (SpySalesOrderQuery::create())->findPk(243);

        $payments = $order
            ->getOrdersJoinSalesPaymentMethodType();

        if ($payments->count() > 0) {
            foreach ($payments as $payment) {
                if ($payment->getSalesPaymentMethodType() !== null) {
                    dump($payment->getSalesPaymentMethodType()->getPaymentMethod());
                }
            }
        }

//        dump($order->getOrdersJoinSalesPaymentMethodType()); die;

        $order = (SpySalesOrderQuery::create())->findPk(
            247
        );

        $salesOrderItems = $this
            ->getSalesOrderItemIds(
                $order
            );

        $result = $this
            ->getFactory()
            ->getOmsFacade()
            ->triggerEventForOrderItems(
                'startCancel',
                $salesOrderItems
            );

        dump($result);
        die;
    }

    public function test3Action()
    {
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpc3MiOiJkcml2ZXIiLCJhdWQiOiJPbGl2ZXJoYXREdXJzdCBBRyIsImp0aSI6IjI1MiIsImlhdCI6MTYzMTU3MjEzNywibmJmIjoxNjMxNTcyMTM3LCJleHAiOjE2MzE2NjQwMDAsInN1YiI6IkRFLS0yNDUiLCJpZEJpbGxpbmciOjUwMywiaWRTaGlwcGluZyI6NTA0LCJpZENvbmNyZXRlVG91ciI6NjgzLCJpZERyaXZlciI6NCwidG91clN0YXJ0Ijp7ImRhdGUiOiIyMDIxLTA5LTE1IDE1OjAwOjAwLjAwMDAwMCIsInRpbWV6b25lX3R5cGUiOjMsInRpbWV6b25lIjoiVVRDIn0sImNhbmNlbE1lc3NhZ2UiOiJDQU5DRUxfTUVTU0FHRV9OT1RfQVRfSE9NRSJ9.tNCzdAEck5LJ01CJ1GZekFf94yiygEiL5yd0FPc_3SJgmxuosRgTo1SNJrqZmU3xk5lE2LN4D43wbECJchIwDw';
        $transfer = $this->getFacade()->getJwtFromToken($token);
        dump($transfer);
        dump($this->getFacade()->checkTransfer($transfer));
        die;
    }

    protected function getSalesOrderItemIds(SpySalesOrder $order): array
    {
        $ids = [];

        foreach ($order->getItems() as $item) {
            $ids[] = $item->getIdSalesOrderItem();
        }

        return $ids;
    }
}
