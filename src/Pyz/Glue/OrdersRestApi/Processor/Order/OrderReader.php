<?php
/**
 * Durst - project - OrderReader.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 16.11.21
 * Time: 10:30
 */

namespace Pyz\Glue\OrdersRestApi\Processor\Order;

use Generated\Shared\Transfer\FilterTransfer;
use Generated\Shared\Transfer\OrderListTransfer;
use Generated\Shared\Transfer\PaginationTransfer;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;
use Spryker\Glue\OrdersRestApi\OrdersRestApiConfig;
use Spryker\Glue\OrdersRestApi\Processor\Order\OrderReader as SprykerOrderReader;

class OrderReader extends SprykerOrderReader
{
    /**
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    protected function getOrderListAttributes(RestRequestInterface $restRequest): RestResponseInterface
    {
        $customerId = $restRequest
            ->getUser()
            ->getSurrogateIdentifier();
        $orderListTransfer = (new OrderListTransfer())
            ->setIdCustomer(
                (int)$customerId
            );

        $limit = 0;
        $filterTransfer = new FilterTransfer();

        if ($restRequest->getPage()) {
            $offset = $restRequest
                ->getPage()
                ->getOffset();
            $limit = $restRequest
                ->getPage()
                ->getLimit();

            $filterTransfer
                ->setOffset(
                    $offset
                )
                ->setLimit(
                    $limit
                );

            $page = $limit ?
                ($offset / $limit + 1) :
                1;

            $orderListTransfer
                ->setPagination(
                    (new PaginationTransfer())
                        ->setPage($page)
                        ->setMaxPerPage($limit)
                );
        }

        $sortTransfer = $restRequest
            ->getSort();

        if (isset($sortTransfer[0])) {
            $filterTransfer
                ->setOrderBy(
                    $sortTransfer[0]
                        ->getField()
                )
                ->setOrderDirection(
                    $sortTransfer[0]
                        ->getDirection()
                );
        }

        $orderListTransfer
            ->setFilter(
                $filterTransfer
            );

        $orderListTransfer = $this
            ->salesClient
            ->getPaginatedOrder(
                $orderListTransfer
            );


        $response = $this
            ->restResourceBuilder
            ->createRestResponse(
                $orderListTransfer->getPagination() !== null ?
                    $orderListTransfer->getPagination()->getNbResults() :
                    0,
                $limit
            );

        foreach ($orderListTransfer->getOrders() as $orderTransfer) {
            $restOrdersAttributesTransfer = $this->orderResourceMapper->mapOrderTransferToRestOrdersAttributesTransfer($orderTransfer);

            $response = $response->addResource(
                $this->restResourceBuilder->createRestResource(
                    OrdersRestApiConfig::RESOURCE_ORDERS,
                    $orderTransfer->getOrderReference(),
                    $restOrdersAttributesTransfer
                )
            );
        }

        return $response;
    }
}
