<?php
/**
 * Durst - project - SendInvalidEmail.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 14.05.20
 * Time: 08:59
 */

namespace Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Command;

use Generated\Shared\Transfer\MailTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\Oms\Communication\Plugin\Mail\MerchantWholesaleOrderInvalidCustomerMailTypePlugin;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Dependency\Plugin\Command\CommandByOrderInterface;
use Spryker\Zed\Sales\Business\Exception\InvalidSalesOrderException;

/**
 * @method \Pyz\Zed\HeidelpayRest\Business\HeidelpayRestFacade getFacade()
 * @method \Pyz\Zed\HeidelpayRest\Communication\HeidelpayRestCommunicationFactory getFactory()
 * @method \Pyz\Zed\HeidelpayRest\HeidelpayRestConfig getConfig()
 */
class SendInvalidEmail extends AbstractPlugin implements CommandByOrderInterface
{
    use GraphmastersTrait;

    public const NAME = 'HeidelpayRest/SendInvalidEmail';

    /**
     * @param array $orderItems
     * @param SpySalesOrder $orderEntity
     * @param ReadOnlyArrayObject $data
     *
     * @return array|void
     *
     * @throws PropelException
     * @throws ContainerKeyNotFoundException
     * @throws InvalidSalesOrderException
     */
    public function run(
        array $orderItems,
        SpySalesOrder $orderEntity,
        ReadOnlyArrayObject $data
    ) {
        $this
            ->sendMail($orderEntity, MerchantWholesaleOrderInvalidCustomerMailTypePlugin::MAIL_TYPE);

        $this->markGraphmastersOrderCancelled($orderEntity);

        return $data->getArrayCopy();
    }

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $salesOrder
     * @param string $mailType
     *
     * @return void
     */
    protected function sendMail(SpySalesOrder $salesOrder, string $mailType)
    {
        $mailTransfer = new MailTransfer();
        $mailTransfer
            ->setType($mailType);

        $branchTransfer = $this
            ->getFactory()
            ->getMerchantFacade()
            ->getBranchById($salesOrder->getFkBranch());

        $mailTransfer
            ->setBranch($branchTransfer);

        $orderTransfer = $this
            ->getFactory()
            ->getSalesFacade()
            ->getDeflatedOrderByIdSalesOrder($salesOrder->getIdSalesOrder());

        $mailTransfer
            ->setOrder($orderTransfer);

        $comments = $this
            ->getFactory()
            ->getSalesFacade()
            ->getCustomerOrderCommentsByIdSalesOrder($salesOrder->getIdSalesOrder())
            ->getComments();

        $mailTransfer
            ->setComments($comments);

        $mailTransfer
            ->setBaseUrl(
                $this
                    ->getConfig()
                    ->getAssetBaseUrl()
            )
            ->setFooterBannerImg($this->getConfig()->getFooterBannerImg())
            ->setFooterBannerLink($this->getConfig()->getFooterBannerLink())
            ->setFooterBannerAlt($this->getConfig()->getFooterBannerAlt())
            ->setFooterBannerCta($this->getConfig()->getFooterBannerCta())
            ->setDurst($this->getFactory()->getOmsFacade()->createDurstCompanyTransfer());

        $this
            ->getFactory()
            ->getMailFacade()
            ->handleMail($mailTransfer);
    }
}
