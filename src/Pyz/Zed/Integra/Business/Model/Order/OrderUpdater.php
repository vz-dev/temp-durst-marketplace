<?php
/**
 * Durst - project - OrderUpdater.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-11-26
 * Time: 10:01
 */

namespace Pyz\Zed\Integra\Business\Model\Order;


use Generated\Shared\Transfer\OrderTransfer;

class OrderUpdater implements OrderUpdaterInterface
{
    /**
     * @param OrderTransfer $orderTransfer
     * @param array $orderData
     *
     * @return OrderTransfer
     */
    public function updateOrderTransferWithIntegraData(OrderTransfer $orderTransfer, array &$orderData) : OrderTransfer
    {
        $orderTransfer
            ->setIntegraReceiptDid($orderData['did'])
            ->setIntegraCustomerNo($orderData['customer_no'])
            ->setDeliveryOrder($orderData['nrTourFolge']);

        foreach ($orderTransfer->getItems() as $item){
            foreach ($orderData['items'] as $itemDid => $itemData)
            {
                if($this->skuStartsWith($item->getMerchantSku(), $itemData['merchant_sku']))
                {
                    $item->setIntegraPositionDid($itemDid);
                }
            }
        }

        return $orderTransfer;
    }

    /**
     * @param string $sku
     * @param string $dataSku
     * @return bool
     */
    protected function skuStartsWith(string $sku, string $dataSku) : bool
    {
        return substr_compare($sku, $dataSku, 0, strlen($sku)) === 0;
    }
}
