<?php
/**
 * Durst - project - BillingPeriod.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 12.05.20
 * Time: 09:54
 */

namespace Pyz\Zed\HeidelpayRest\Business\Transaction\MetaData;

use DateTime;
use Generated\Shared\Transfer\BillingPeriodTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use heidelpayPHP\Exceptions\HeidelpayApiException;
use heidelpayPHP\Resources\Metadata;
use Pyz\Shared\HeidelpayRest\HeidelpayRestConstants;
use Pyz\Zed\Billing\Business\BillingFacadeInterface;
use Pyz\Zed\Billing\Business\Exception\BillingPeriodEntityNotFoundException;
use Pyz\Zed\HeidelpayRest\Business\Transaction\Log\LoggerInterface;
use Pyz\Zed\HeidelpayRest\Business\Util\ClientWrapperInterface;
use Pyz\Zed\HeidelpayRest\HeidelpayRestConfig;

class BillingPeriod implements BillingPeriodInterface
{
    protected const KEY_BILLING_REFERENCE = 'Durst-Abrechnungsreferenznr.';

    /**
     * @var ClientWrapperInterface
     */
    protected $clientWrapper;

    /**
     * @var BillingFacadeInterface
     */
    protected $billingFacade;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var HeidelpayRestConfig
     */
    protected $config;

    /**
     * BillingPeriod constructor.
     *
     * @param ClientWrapperInterface $clientWrapper
     * @param BillingFacadeInterface $billingFacade
     * @param LoggerInterface $logger
     * @param HeidelpayRestConfig $config
     */
    public function __construct(
        ClientWrapperInterface $clientWrapper,
        BillingFacadeInterface $billingFacade,
        LoggerInterface $logger,
        HeidelpayRestConfig $config
    ) {
        $this->clientWrapper = $clientWrapper;
        $this->billingFacade = $billingFacade;
        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     *
     * @param OrderTransfer $orderTransfer
     *
     * @return Metadata|null
     */
    public function getBillingPeriodMetaData(OrderTransfer $orderTransfer): ?Metadata
    {
        $deliveryStart = $orderTransfer->getGmStartTime() ?? $orderTransfer->getConcreteTimeSlot()->getStartTime();

        try {
            $billingPeriod = $this
                ->billingFacade
                ->getBillingPeriodByTimeAndBranch(
                    new DateTime($deliveryStart),
                    $orderTransfer->getFkBranch()
                );

            if ($billingPeriod->getHeidelpayRestMetaDataId() === null) {
                $metaData = $this->createMetaData($billingPeriod, $orderTransfer);

                if ($metaData !== null) {
                    $billingPeriod->setHeidelpayRestMetaDataId(
                        $metaData->getId()
                    );
                }

                $this->billingFacade->updateBillingPeriod($billingPeriod);

                return $metaData;
            }

            return $this
                ->getMetaData($billingPeriod->getHeidelpayRestMetaDataId(), $orderTransfer);
        } catch (BillingPeriodEntityNotFoundException $e) {
            return null;
        }
    }

    /**
     * @param string $idMetaData
     * @param OrderTransfer $orderTransfer
     *
     * @return Metadata|null
     */
    protected function getMetaData(string $idMetaData, OrderTransfer $orderTransfer): ?Metadata
    {
        try {
            return $this
                ->clientWrapper
                ->getHeidelpayClient($orderTransfer)
                ->fetchMetadata($idMetaData);
        } catch (HeidelpayApiException $e) {
            $this
                ->logger
                ->logError(
                    $e,
                    $orderTransfer->getIdSalesOrder(),
                    null,
                    HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_CREATE
                );
        }

        return null;
    }

    /**
     * @param BillingPeriodTransfer $billingPeriodTransfer
     * @param OrderTransfer $orderTransfer
     *
     * @return Metadata|null
     */
    protected function createMetaData(BillingPeriodTransfer $billingPeriodTransfer, OrderTransfer $orderTransfer): ?Metadata
    {
        $heidelpayMetaData = new Metadata();
        $heidelpayMetaData->addMetadata(
            static::KEY_BILLING_REFERENCE,
            $billingPeriodTransfer->getBillingReference()
        );

        try {
            $heidelpayMetaData = $this
                ->clientWrapper
                ->getHeidelpayClient($orderTransfer)
                ->createMetadata($heidelpayMetaData);

            $this->logger->logMetaData($heidelpayMetaData, $orderTransfer->getIdSalesOrder());

            return $heidelpayMetaData;
        } catch (HeidelpayApiException $e) {
            $this->logger->logError(
                $e,
                $orderTransfer->getIdSalesOrder(),
                null,
                HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_CREATE
            );
        }

        return null;
    }
}
