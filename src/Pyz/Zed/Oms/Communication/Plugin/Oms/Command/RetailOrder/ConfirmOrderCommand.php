<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 19.02.18
 * Time: 13:19
 */

namespace Pyz\Zed\Oms\Communication\Plugin\Oms\Command\RetailOrder;

use Generated\Shared\Transfer\MailTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Pyz\Zed\Oms\Communication\OmsCommunicationFactory;
use Pyz\Zed\Oms\Communication\Plugin\Mail\MerchantOrderConfirmCustomerTypePlugin;
use Pyz\Zed\Oms\Communication\Plugin\Mail\MerchantOrderConfirmMerchantTypePlugin;
use Pyz\Zed\Oms\OmsConfig;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Communication\Plugin\Oms\Command\AbstractCommand;
use Spryker\Zed\Oms\Dependency\Plugin\Command\CommandByOrderInterface;

/**
 * Class ConfirmOrderCommand
 * @package Pyz\Zed\Oms\Communication\Plugin\Oms\Command\RetailOrder
 * @method OmsCommunicationFactory getFactory()
 * @method \Pyz\Zed\Oms\Business\OmsFacadeInterface getFacade()
 * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
 * @method OmsConfig getConfig()
 */
class ConfirmOrderCommand extends AbstractCommand implements CommandByOrderInterface
{
    public const COMMENT_TYPE_CUSTOMER = 'COMMENT_CUSTOMER';

    /**
     * @param array $orderItems
     * @param SpySalesOrder $order
     * @param ReadOnlyArrayObject $data
     * @return array
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function run(array $orderItems, SpySalesOrder $order, ReadOnlyArrayObject $data)
    {
        $this->sendMail($order, MerchantOrderConfirmMerchantTypePlugin::MAIL_TYPE);
        $this->sendMail($order, MerchantOrderConfirmCustomerTypePlugin::MAIL_TYPE);

        return [];
    }

    /**
     * @param SpySalesOrder $order
     * @param array $items
     * @param string $mailType
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function sendMail(SpySalesOrder $order, string $mailType)
    {
        $mailTransfer = new MailTransfer();
        $mailTransfer->setType($mailType);

        $branchTransfer = $this
            ->getFactory()
            ->getMerchantFacade()
            ->getBranchById($order->getFkBranch());
        $mailTransfer->setBranch($branchTransfer);

        $orderTransfer = $this
            ->getFactory()
            ->getSalesFacade()
            ->getOrderByIdSalesOrder($order->getIdSalesOrder());
        $mailTransfer->setOrder($orderTransfer);

        $comments = $this
            ->getFactory()
            ->getSalesFacade()
            ->getCustomerOrderCommentsByIdSalesOrder($order->getIdSalesOrder())
            ->getComments();

        $mailTransfer->setComments($comments);

        $mailTransfer
            ->setBaseUrl($this->getConfig()->getBaseUrl())
            ->setFooterBannerImg($this->getConfig()->getFooterBannerImg())
            ->setFooterBannerLink($this->getConfig()->getFooterBannerLink())
            ->setFooterBannerAlt($this->getConfig()->getFooterBannerAlt())
            ->setFooterBannerCta($this->getConfig()->getFooterBannerCta())
            ->setDurst($this->getFacade()->createDurstCompanyTransfer());

        $this
            ->getFactory()
            ->getMailFacade()
            ->handleMail($mailTransfer);
    }
}
