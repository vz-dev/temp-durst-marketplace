<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-01-30
 * Time: 15:29
 */

namespace Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder;


use DateTime;
use Exception;
use Generated\Shared\Transfer\MailTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\Oms\Business\OmsFacadeInterface;
use Pyz\Zed\Oms\Communication\OmsCommunicationFactory;
use Pyz\Zed\Oms\Communication\Plugin\Mail\MerchantOrderConfirmMerchantTypePlugin;
use Pyz\Zed\Oms\Communication\Plugin\Mail\MerchantWholesaleOrderConfirmCustomerTypePlugin;
use Pyz\Zed\Oms\OmsConfig;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Communication\Plugin\Oms\Command\AbstractCommand;
use Spryker\Zed\Oms\Dependency\Plugin\Command\CommandByOrderInterface;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;
use Spryker\Zed\Sales\Business\Exception\InvalidSalesOrderException;

/**
 * Class ConfirmOrderCommand
 * @package Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder
 * @method OmsCommunicationFactory getFactory()
 * @method OmsConfig getConfig()
 * @method OmsFacadeInterface getFacade()
 */
class ConfirmOrderCommand extends AbstractCommand implements CommandByOrderInterface
{
    /**
     * @param array $orderItems
     * @param SpySalesOrder $orderEntity
     * @param ReadOnlyArrayObject $data
     * @return array
     */
    public function run(array $orderItems, SpySalesOrder $orderEntity, ReadOnlyArrayObject $data): array
    {

            $this
                ->sendMail($orderEntity, MerchantOrderConfirmMerchantTypePlugin::MAIL_TYPE);

           $this
                ->sendMail($orderEntity, MerchantWholesaleOrderConfirmCustomerTypePlugin::MAIL_TYPE);

            $confirmationDate = new DateTime('now');

        $this
            ->getFactory()
            ->getSalesFacade()
            ->updateConfirmationDate(
                $orderEntity
                    ->getIdSalesOrder(),
                $confirmationDate
            );

        return [];
    }

    /**
     * @param SpySalesOrder $salesOrder
     * @param string $mailType
     * @throws PropelException
     * @throws AmbiguousComparisonException
     * @throws InvalidSalesOrderException
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
                ->getBaseUrl()
            )
            ->setFooterBannerImg($this->getConfig()->getFooterBannerImg())
            ->setFooterBannerLink($this->getConfig()->getFooterBannerLink())
            ->setFooterBannerAlt($this->getConfig()->getFooterBannerAlt())
            ->setFooterBannerCta($this->getConfig()->getFooterBannerCta())
            ->setDurst($this->getFacade()->createDurstCompanyTransfer());

        $token = null;
        if($orderTransfer->getConcreteTimeSlot() !== null && $orderTransfer->getConcreteTimeSlot()->getFkConcreteTour() !== null)
        {
            $token = $this
                ->getFactory()
                ->getCancelOrderFacade()
                ->getTokenForCustomerMail(
                    $orderTransfer
                        ->getIdSalesOrder()
                );
        }

        if ($token !== null) {
            try {
                $jwt = $this
                    ->getFactory()
                    ->getCancelOrderFacade()
                    ->getJwtFromToken(
                        $token
                    );

                $mailTransfer
                    ->setCancelOrderLink(
                        sprintf(
                            $this
                                ->getConfig()
                                ->getCancelOrderBaseUrl(),
                            $token
                        )
                    )
                    ->setCancelOrderExpiration(
                        $jwt
                            ->getExpiration()
                    );
            } catch (Exception $exception) {
                // do not set any cancel order link or expire date
            }
        }

        $mailTransfer
            ->setCancelOrderToken($token);

        $this
            ->getFactory()
            ->getMailFacade()
            ->handleMail($mailTransfer);
    }
}
