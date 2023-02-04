<?php
/**
 * Durst - project - HeidelpayRestZedStub.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 17.01.19
 * Time: 12:06
 */

namespace Pyz\Client\HeidelpayRest\Zed;


use Generated\Shared\Transfer\HeidelpayRestAuthorizationTransfer;

class HeidelpayRestZedStub implements HeidelpayRestZedStubInterface
{
    protected const URL_AUTHORIZATION_STATUS = '/heidelpay-rest/gateway/get-status-for-authorization';
    protected const URL_AUTHORIZATION_STATUS_ORDER_REF = '/heidelpay-rest/gateway/get-status-for-authorization-by-order-ref';

    /**
     * @var \Spryker\Client\ZedRequest\ZedRequestClientInterface
     */
    protected $zedRequestClient;

    /**
     * HeidelpayRestZedStub constructor.
     * @param \Spryker\Client\ZedRequest\ZedRequestClientInterface $zedRequestClient
     */
    public function __construct(\Spryker\Client\ZedRequest\ZedRequestClientInterface $zedRequestClient)
    {
        $this->zedRequestClient = $zedRequestClient;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $idPayment
     * @return string
     */
    public function getAuthorizationStatusByPaymentId(string $idPayment): HeidelpayRestAuthorizationTransfer
    {
        /** @var HeidelpayRestAuthorizationTransfer $responseTransfer */
        $responseTransfer = $this
            ->zedRequestClient
            ->call(
                static::URL_AUTHORIZATION_STATUS,
                ($this->createAuthorizationRequestTransfer())
                    ->setPaymentId($idPayment)
            );

        return $responseTransfer;

    }

    /**
     * @param string $orderRef
     * @return HeidelpayRestAuthorizationTransfer
     */
    public function getAuthorizationStatusBySalesOrderRef(string $orderRef): HeidelpayRestAuthorizationTransfer
    {
        /** @var HeidelpayRestAuthorizationTransfer $responseTransfer */
        $responseTransfer = $this
            ->zedRequestClient
            ->call(
                static::URL_AUTHORIZATION_STATUS_ORDER_REF,
                ($this->createAuthorizationRequestTransfer())
                    ->setOrderRef($orderRef)
            );

        return $responseTransfer;

    }

    /**
     * @param string $idPayment
     * @return \Generated\Shared\Transfer\HeidelpayRestAuthorizationTransfer
     */
    protected function createAuthorizationRequestTransfer(): HeidelpayRestAuthorizationTransfer
    {
        return new HeidelpayRestAuthorizationTransfer();
    }
}
