<?php
/**
 * Durst - project - HeidelpayRestClient.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 15.01.19
 * Time: 15:08
 */

namespace Pyz\Client\HeidelpayRest;

use Generated\Shared\Transfer\HeidelpayRestAuthorizationTransfer;
use Spryker\Client\Kernel\AbstractClient;

/**
 * Class HeidelpayRestClient
 * @package Pyz\Client\HeidelpayRest
 * @method \Pyz\Client\HeidelpayRest\HeidelpayRestFactory getFactory()
 */
class HeidelpayRestClient extends AbstractClient implements HeidelpayRestClientInterface
{
    /**
     * {@inheritdoc}
     *
     * @param string $idPayment
     * @return string
     */
    public function getAuthorizationStatusByPaymentId(string $idPayment): HeidelpayRestAuthorizationTransfer
    {
        return $this
            ->getFactory()
            ->createHeidelpayRestZedStub()
            ->getAuthorizationStatusByPaymentId($idPayment);
    }

    /**
     * @param string $orderRef
     *
     * @return HeidelpayRestAuthorizationTransfer
     */
    public function getAuthorizationStatusBySalesOrderRef(string $orderRef): HeidelpayRestAuthorizationTransfer
    {
        return $this
            ->getFactory()
            ->createHeidelpayRestZedStub()
            ->getAuthorizationStatusBySalesOrderRef($orderRef);
    }
}
