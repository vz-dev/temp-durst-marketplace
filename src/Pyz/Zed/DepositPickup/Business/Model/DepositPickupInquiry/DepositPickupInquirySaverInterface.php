<?php

namespace Pyz\Zed\DepositPickup\Business\Model\DepositPickupInquiry;

use Generated\Shared\Transfer\DepositPickupInquiryTransfer;

interface DepositPickupInquirySaverInterface
{
    /**
     * @param DepositPickupInquiryTransfer $inquiryTransfer
     */
    public function saveDepositPickupInquiry(DepositPickupInquiryTransfer $inquiryTransfer): void;
}
