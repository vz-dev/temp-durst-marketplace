<?php

namespace Pyz\Zed\Oms\Business\Model\Mail;

use Generated\Shared\Transfer\BillingPeriodTransfer;
use Generated\Shared\Transfer\BranchTransfer;

interface BillingMailManagerInterface
{
    /**
     * @param BranchTransfer $branchTransfer
     * @param BillingPeriodTransfer $billingPeriodTransfer
     */
    public function sendMail(BranchTransfer $branchTransfer, BillingPeriodTransfer $billingPeriodTransfer): void;
}
