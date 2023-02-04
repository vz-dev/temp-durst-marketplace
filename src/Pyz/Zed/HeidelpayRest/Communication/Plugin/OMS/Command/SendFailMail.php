<?php
/**
 * Durst - project - SendFailMail.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 29.07.20
 * Time: 10:25
 */

namespace Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Command;


use ArrayObject;
use Generated\Shared\Transfer\MailRecipientTransfer;
use Generated\Shared\Transfer\MailTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\Oms\Communication\Plugin\Mail\MerchantWholesaleOrderFailStateMailTypePlugin;
use Spryker\Service\UtilText\Model\Url\Url;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Dependency\Plugin\Command\CommandByOrderInterface;
use Spryker\Zed\Sales\Business\Exception\InvalidSalesOrderException;

/**
 * Class SendFailMail
 * @package Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Command
 * @method \Pyz\Zed\HeidelpayRest\Communication\HeidelpayRestCommunicationFactory getFactory()
 * @method \Pyz\Zed\HeidelpayRest\HeidelpayRestConfig getConfig()
 */
class SendFailMail extends AbstractPlugin implements CommandByOrderInterface
{
    use GraphmastersTrait;

    public const NAME = 'HeidelpayRest/SendFailMail';

    protected const URL_DETAIL = '/sales/detail/index';
    protected const PARAM_ID_SALES_ORDER = 'id-sales-order';

    /**
     * {@inheritDoc}
     *
     * @param array $orderItems
     * @param SpySalesOrder $orderEntity
     * @param ReadOnlyArrayObject $data
     * @return array
     * @throws ContainerKeyNotFoundException
     * @throws PropelException
     * @throws InvalidSalesOrderException
     */
    public function run(array $orderItems, SpySalesOrder $orderEntity, ReadOnlyArrayObject $data): array
    {
        $this
            ->sendMail(
                $orderEntity,
                MerchantWholesaleOrderFailStateMailTypePlugin::MAIL_TYPE
            );

        $this->markGraphmastersOrderCancelled($orderEntity);

        return $data
            ->getArrayCopy();
    }

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $order
     * @param string $mailType
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function sendMail(
        SpySalesOrder $order,
        string $mailType
    ): void
    {
        $mailTransfer = new MailTransfer();

        $orderStates = $this
            ->getFactory()
            ->getSalesFacade()
            ->getDistinctOrderStates(
                $order
                    ->getIdSalesOrder()
            );

        $subject = sprintf(
            $this
                ->getConfig()
                ->getOmsErrorMailSubject(),
            $order
                ->getIdSalesOrder(),
            implode(',', $orderStates)
        );

        /* @var $firstItem \Orm\Zed\Sales\Persistence\SpySalesOrderItem */
        $firstItem = $order
            ->getItems()
            ->getFirst();

        $orderTransfer = $this
            ->getOrderTransferDeflated(
                $order
                    ->getIdSalesOrder()
            );

        $mailTransfer
            ->setFridgeUrl($this->createFridgeUrl($order))
            ->setOrder($orderTransfer)
            ->setProcessName($firstItem->getState()->getName())
            ->setRecipients($this->createMailRecipients())
            ->setSubject($subject)
            ->setType($mailType);

        $this
            ->getFactory()
            ->getMailFacade()
            ->handleMail(
                $mailTransfer
            );
    }

    /**
     * @return \ArrayObject|MailRecipientTransfer[]
     */
    protected function createMailRecipients(): ArrayObject
    {
        $recipients = new ArrayObject();

        foreach ($this->getConfig()->getOmsErrorMailRecipients() as $omsErrorMailRecipientEmail => $omsErrorMailRecipientName) {
            $recipient = (new MailRecipientTransfer())
                ->setEmail($omsErrorMailRecipientEmail)
                ->setName($omsErrorMailRecipientName);

            $recipients
                ->append(
                    $recipient
                );
        }

        return $recipients;
    }

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $order
     * @return string
     */
    protected function createFridgeUrl(SpySalesOrder $order): string
    {
        $queryPath = Url::generate(
            static::URL_DETAIL,
            [
                static::PARAM_ID_SALES_ORDER => $order->getIdSalesOrder()
            ]
        );

        return sprintf(
            '%s%s',
            $this
                ->getConfig()
                ->getHostName(),
            $queryPath
        );
    }

    /**
     * @param int $idSalesOrder
     * @return \Generated\Shared\Transfer\OrderTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getOrderTransferDeflated(int $idSalesOrder): OrderTransfer
    {
        return $this
            ->getFactory()
            ->getSalesFacade()
            ->getDeflatedOrderByIdSalesOrder(
                $idSalesOrder
            );
    }
}
