<?php
/**
 * Durst - project - Customer.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 19.05.20
 * Time: 09:47
 */

namespace Pyz\Zed\HeidelpayRest\Business\Transaction\Resource;

use Generated\Shared\Transfer\OrderTransfer;
use heidelpayPHP\Exceptions\HeidelpayApiException;
use heidelpayPHP\Resources\Customer as HeidelpayCustomer;
use Pyz\Shared\HeidelpayRest\HeidelpayRestConstants;
use Pyz\Zed\HeidelpayRest\Business\Transaction\Log\LoggerInterface;
use Pyz\Zed\HeidelpayRest\Business\Util\ClientWrapperInterface;

class Customer implements CustomerInterface
{
    /**
     * @var \Pyz\Zed\HeidelpayRest\Business\Util\ClientWrapperInterface
     */
    protected $clientWrapper;

    /**
     * @var \Pyz\Zed\HeidelpayRest\Business\Transaction\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Customer constructor.
     *
     * @param \Pyz\Zed\HeidelpayRest\Business\Util\ClientWrapperInterface $clientWrapper
     * @param \Pyz\Zed\HeidelpayRest\Business\Transaction\Log\LoggerInterface $logger
     */
    public function __construct(
        ClientWrapperInterface $clientWrapper,
        LoggerInterface $logger
    ) {
        $this->clientWrapper = $clientWrapper;
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \heidelpayPHP\Resources\Customer|null
     */
    public function getCustomer(OrderTransfer $orderTransfer): ?HeidelpayCustomer
    {
        if ($orderTransfer->getHeidelpayRestCustomerId() === null) {
            return null;
        }

        try {
            return $this
                ->clientWrapper
                ->getHeidelpayClient($orderTransfer)
                ->fetchCustomer($orderTransfer->getHeidelpayRestCustomerId());
        } catch (HeidelpayApiException $e) {
            $this
                ->logger
                ->logError(
                    $e,
                    $orderTransfer->getIdSalesOrder(),
                    null,
                    HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_FETCH
                );

            return null;
        }
    }
}
