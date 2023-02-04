<?php
/**
 * Durst - project - SendInvoiceInvoice.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 21.01.20
 * Time: 12:50
 */

namespace Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder;

use Generated\Shared\Transfer\MailTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Pyz\Shared\HeidelpayRest\HeidelpayRestConstants;
use Pyz\Zed\Oms\Communication\Plugin\Mail\MerchantOrderInvoiceInvoiceMailTypePlugin;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Dependency\Plugin\Command\CommandByOrderInterface;

/**
 * @method \Pyz\Zed\Oms\Business\OmsFacade getFacade()
 * @method \Pyz\Zed\Oms\Communication\OmsCommunicationFactory getFactory()
 */
class SendInvoiceInvoice extends AbstractPlugin implements CommandByOrderInterface
{
    public const NAME = 'WholesaleOrder/SendInvoiceInvoice';

    /**
     * @inheritDoc
     */
    public function run(
        array $orderItems,
        SpySalesOrder $orderEntity,
        ReadOnlyArrayObject $data
    ) {
        $orderTransfer = $this
            ->getFactory()
            ->getSalesFacade()
            ->getOrderByIdSalesOrder($orderEntity->getIdSalesOrder());

        /**
         * @deprecated
         */
        if($this->isOldWholeSaleProcess($orderEntity) !== true){
            $orderTransfer = $this
                ->getFactory()
                ->getSalesFacade()
                ->getDeflatedOrderByIdSalesOrder($orderEntity->getIdSalesOrder());
        }

        $processingInfo = $this
            ->getFactory()
            ->getHeidelpayRestFacade()
            ->getProcessingInformationForOrder($orderEntity->getIdSalesOrder());

        $this
            ->getFacade()
            ->sendInvoiceMail(
                $orderTransfer,
                $orderTransfer->getBranch(),
                MerchantOrderInvoiceInvoiceMailTypePlugin::MAIL_TYPE,
                $this->createMailTransfer(
                    $processingInfo,
                    $orderEntity->getIdSalesOrder()
                )
            );

        return $data->getArrayCopy();
    }

    /**
     * @param array $data
     * @param int $idSalesOrder
     * @return \Generated\Shared\Transfer\MailTransfer
     */
    protected function createMailTransfer(
        array $data,
        int $idSalesOrder
    ): MailTransfer
    {
        $deliveryTime = $this
            ->getFacade()
            ->getDeliveryTimeFromTransitionLogByIdSalesOrder(
                $idSalesOrder
            );

        return (new MailTransfer())
            ->setHeidelpayBic($data[HeidelpayRestConstants::HEIDELPAY_REST_INVOICE_KEY_BIC])
            ->setHeidelpayIban($data[HeidelpayRestConstants::HEIDELPAY_REST_INVOICE_KEY_IBAN])
            ->setHeidelpayHolder($data[HeidelpayRestConstants::HEIDELPAY_REST_INVOICE_KEY_HOLDER])
            ->setHeidelpayDescriptor($data[HeidelpayRestConstants::HEIDELPAY_REST_INVOICE_KEY_DESCRIPTOR])
            ->setDeliveryTime($deliveryTime)
            ->setDurst($this->getFacade()->createDurstCompanyTransfer());
    }

    /**
     * @param SpySalesOrder $order
     * @return bool
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function isOldWholeSaleProcess(SpySalesOrder $order) : bool
    {
        foreach ($order->getItems() as $item){
            if(in_array($item->getProcess()->getName(), $this->getFactory()->getConfig()->getOldWholesaleProcess()) === true){
                return true;
            }
        }

        return false;
    }
}
