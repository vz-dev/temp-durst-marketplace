<?php
/**
 * Durst - project - SendCancelMail.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 08.09.21
 * Time: 17:03
 */

namespace Pyz\Zed\CancelOrder\Communication\Plugin\OMS\Command;

use Exception;
use Generated\Shared\Transfer\MailTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Pyz\Shared\HeidelpayRest\HeidelpayRestConstants;
use Pyz\Zed\CancelOrder\Business\Exception\CancelOrderUnknownPaymentTypeException;
use Pyz\Zed\CancelOrder\CancelOrderConfig;
use Pyz\Zed\CancelOrder\Communication\CancelOrderCommunicationFactory;
use Pyz\Zed\CancelOrder\Communication\Plugin\Mail\CancelOrderCashTypePlugin;
use Pyz\Zed\CancelOrder\Communication\Plugin\Mail\CancelOrderCreditCardTypePlugin;
use Pyz\Zed\CancelOrder\Communication\Plugin\Mail\CancelOrderInvoiceGuaranteedTypePlugin;
use Pyz\Zed\CancelOrder\Communication\Plugin\Mail\CancelOrderInvoiceTypePlugin;
use Pyz\Zed\CancelOrder\Communication\Plugin\Mail\CancelOrderPaypalTypePlugin;
use Pyz\Zed\CancelOrder\Communication\Plugin\Mail\CancelOrderSepaDirectDebitTypePlugin;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Communication\Plugin\Oms\Command\AbstractCommand;
use Spryker\Zed\Oms\Dependency\Plugin\Command\CommandByOrderInterface;

/**
 * Class SendCancelMail
 * @package Pyz\Zed\CancelOrder\Communication\Plugin\OMS\Command
 *
 * @method CancelOrderCommunicationFactory getFactory()
 * @method CancelOrderConfig getConfig()
 */
class SendCancelMail extends AbstractCommand implements CommandByOrderInterface
{
    public const EVENT_ID = 'sendCancelMail';
    public const NAME = 'CancelOrder/SendCancelMail';
    public const STATE_NAME = 'send cancel mail';

    /**
     * {@inheritDoc}
     *
     * @param array $orderItems
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $orderEntity
     * @param \Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject $data
     * @return array
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\CancelOrder\Business\Exception\CancelOrderUnknownPaymentTypeException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     * @throws \Spryker\Zed\Sales\Business\Exception\InvalidSalesOrderException
     */
    public function run(
        array $orderItems,
        SpySalesOrder $orderEntity,
        ReadOnlyArrayObject $data
    ): array
    {
        $paymentMethod = $this
            ->getPaymentFromOrderEntity(
                $orderEntity
            );

        switch ($paymentMethod) {
            case HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_METHOD_CASH_ON_DELIVERY:
                $mailType = CancelOrderCashTypePlugin::MAIL_TYPE;
                break;
            case HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_METHOD_CREDIT_CARD_AUTHORIZE:
                $mailType = CancelOrderCreditCardTypePlugin::MAIL_TYPE;
                break;
            case HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_METHOD_INVOICE:
                $mailType = CancelOrderInvoiceTypePlugin::MAIL_TYPE;
                break;
            case HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_METHOD_INVOICE_GUARANTEED:
                $mailType = CancelOrderInvoiceGuaranteedTypePlugin::MAIL_TYPE;
                break;
            case HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_METHOD_PAY_PAL_AUTHORIZE:
                $mailType = CancelOrderPaypalTypePlugin::MAIL_TYPE;
                break;
            case HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_METHOD_SEPA_DIRECT_DEBIT:
            case HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_METHOD_SEPA_DIRECT_DEBIT_GUARANTEED:
                $mailType = CancelOrderSepaDirectDebitTypePlugin::MAIL_TYPE;
                break;
            default:
                throw new CancelOrderUnknownPaymentTypeException(
                    sprintf(
                        CancelOrderUnknownPaymentTypeException::MESSAGE,
                        $paymentMethod
                    )
                );
        }

        $this
            ->sendMail(
                $orderEntity,
                $mailType
            );

        return [];
    }

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $order
     * @return string|null
     */
    protected function getPaymentFromOrderEntity(
        SpySalesOrder $order
    ): ?string
    {
        try {
            $payments = $order
                ->getOrdersJoinSalesPaymentMethodType();

            if ($payments->count() > 0) {
                foreach ($payments as $payment) {
                    if ($payment->getSalesPaymentMethodType() !== null) {
                        return $payment
                            ->getSalesPaymentMethodType()
                            ->getPaymentMethod();
                    }
                }
            }
        } catch (Exception $exception) {
            return null;
        }

        return null;
    }

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $order
     * @param string|null $mailType
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     * @throws \Spryker\Zed\Sales\Business\Exception\InvalidSalesOrderException
     */
    protected function sendMail(
        SpySalesOrder $order,
        ?string $mailType
    ): void
    {
        $mail = new MailTransfer();

        $branchTransfer = $this
            ->getFactory()
            ->getMerchantFacade()
            ->getBranchById(
                $order
                    ->getFkBranch()
            );

        $orderTransfer = $this
            ->getFactory()
            ->getSalesFacade()
            ->getDeflatedOrderByIdSalesOrder(
                $order
                    ->getIdSalesOrder()
            );

        $mail
            ->setType(
                $mailType
            )
            ->setBranch(
                $branchTransfer
            )
            ->setOrder(
                $orderTransfer
            )
            ->setBaseUrl(
                $this
                    ->getConfig()
                    ->getBaseUrl()
            )
            ->setFooterBannerImg(
                $this
                    ->getConfig()
                    ->getFooterBannerImg()
            )
            ->setFooterBannerLink(
                $this
                    ->getConfig()
                    ->getFooterBannerLink()
            )
            ->setFooterBannerAlt(
                $this
                    ->getConfig()
                    ->getFooterBannerAlt()
            )
            ->setFooterBannerCta(
                $this
                    ->getConfig()
                    ->getFooterBannerCta()
            )
            ->setDurst(
                $this
                    ->getFactory()
                    ->getOmsFacade()
                    ->createDurstCompanyTransfer()
            );

        $this
            ->getFactory()
            ->getMailFacade()
            ->handleMail(
                $mail
            );
    }
}
