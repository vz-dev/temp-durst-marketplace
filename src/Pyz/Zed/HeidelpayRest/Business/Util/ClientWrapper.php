<?php
/**
 * Durst - project - ClientWrapper.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 19.05.20
 * Time: 09:50
 */

namespace Pyz\Zed\HeidelpayRest\Business\Util;

use Generated\Shared\Transfer\OrderTransfer;
use heidelpayPHP\Heidelpay;

class ClientWrapper implements ClientWrapperInterface
{
    /**
     * @var \heidelpayPHP\Heidelpay
     */
    protected $heidelpayClient;

    /**
     * ClientWrapper constructor.
     *
     * @param \heidelpayPHP\Heidelpay $heidelpayClient
     */
    public function __construct(Heidelpay $heidelpayClient)
    {
        $this->heidelpayClient = $heidelpayClient;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \heidelpayPHP\Heidelpay
     */
    public function getHeidelpayClient(OrderTransfer $orderTransfer): Heidelpay
    {
        $branchTransfer = $orderTransfer
            ->requireBranch()
            ->getBranch();

        if ($branchTransfer->getHeidelpayPrivateKey() !== null) {
            $this
                ->heidelpayClient
                ->setKey($branchTransfer->getHeidelpayPrivateKey());
        }

        return $this->heidelpayClient;
    }
}
