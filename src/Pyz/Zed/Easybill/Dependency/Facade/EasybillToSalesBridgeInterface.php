<?php
/**
 * Durst - project - EasybillToSalesBridgeInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 06.01.20
 * Time: 15:40
 */

namespace Pyz\Zed\Easybill\Dependency\Facade;


use Generated\Shared\Transfer\OrderTransfer;

interface EasybillToSalesBridgeInterface
{
    /**
     * @param int $idSalesOrder
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    public function getOrderByIdSalesOrder(int $idSalesOrder): OrderTransfer;
}
