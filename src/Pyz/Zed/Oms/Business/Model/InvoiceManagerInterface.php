<?php
/**
 * Durst - merchant_center - InvoiceManagerInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 07.08.18
 * Time: 13:53
 */

namespace Pyz\Zed\Oms\Business\Model;


interface InvoiceManagerInterface
{
    /**
     * @param int $idSalesOrder
     * @return bool
     */
    public function addInvoiceNumberToOrder(int $idSalesOrder) : bool;
}