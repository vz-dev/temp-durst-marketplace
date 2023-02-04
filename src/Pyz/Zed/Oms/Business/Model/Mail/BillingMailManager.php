<?php

namespace Pyz\Zed\Oms\Business\Model\Mail;

use Generated\Shared\Transfer\BillingPeriodTransfer;
use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\MailTransfer;
use Pyz\Zed\Oms\Communication\Plugin\Mail\MerchantWholesaleOrderBillingUpdateMailTypePlugin;
use Pyz\Zed\Oms\OmsConfig;
use Spryker\Zed\Oms\Dependency\Facade\OmsToMailInterface;

class BillingMailManager implements BillingMailManagerInterface
{
    /**
     * @var OmsToMailInterface
     */
    protected $mailFacade;

    /**
     * @var OmsConfig
     */
    protected $config;

    /**
     * BillingMailManager constructor.
     *
     * @param OmsToMailInterface $mailFacade
     * @param OmsConfig $config
     */
    public function __construct(OmsToMailInterface $mailFacade, OmsConfig $config)
    {
        $this->mailFacade = $mailFacade;
        $this->config = $config;
    }

    /**
     * @param BranchTransfer $branchTransfer
     * @param BillingPeriodTransfer $billingPeriodTransfer
     */
    public function sendMail(BranchTransfer $branchTransfer, BillingPeriodTransfer $billingPeriodTransfer): void
    {
        $mailTransfer = $this
            ->createMailTransfer()
            ->setType(MerchantWholesaleOrderBillingUpdateMailTypePlugin::MAIL_TYPE)
            ->setBranch($branchTransfer)
            ->setBillingPeriod($billingPeriodTransfer)
            ->setBaseUrl($this->config->getBaseUrl());

        $this
            ->mailFacade
            ->handleMail($mailTransfer);
    }

    /**
     * @return MailTransfer
     */
    protected function createMailTransfer(): MailTransfer
    {
        return new MailTransfer();
    }
}
