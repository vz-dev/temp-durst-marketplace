<?php
/**
 * Durst - project - OrdersRestApiFactory.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 16.11.21
 * Time: 10:29
 */

namespace Pyz\Glue\OrdersRestApi;

use Pyz\Glue\OrdersRestApi\Processor\Order\OrderReader;
use Spryker\Glue\OrdersRestApi\OrdersRestApiFactory as SprykerOrdersRestApiFactory;
use Spryker\Glue\OrdersRestApi\Processor\Order\OrderReaderInterface;

class OrdersRestApiFactory extends SprykerOrdersRestApiFactory
{
    /**
     * @return \Spryker\Glue\OrdersRestApi\Processor\Order\OrderReaderInterface
     */
    public function createOrderReader(): OrderReaderInterface
    {
        return new OrderReader(
            $this
                ->getSalesClient(),
            $this
                ->getResourceBuilder(),
            $this
                ->createOrderResourceMapper()
        );
    }
}
