<?php
/**
 * Durst - project - RefundMailManager.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2019-02-21
 * Time: 13:03
 */

namespace Pyz\Zed\Oms\Business\Model\Mail;

use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\DriverTransfer;
use Generated\Shared\Transfer\MailTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Pyz\Zed\Oms\Business\Model\Durst\DurstCompanyDetailsManagerInterface;
use Pyz\Zed\Oms\Communication\Plugin\Mail\MerchantWholeSaleOrderRefundMailTypePlugin;
use Pyz\Zed\Oms\OmsConfig;
use Spryker\Zed\Oms\Dependency\Facade\OmsToMailInterface;

class RefundMailManager implements RefundMailManagerInterface
{
    /**
     * @var \Spryker\Zed\Oms\Dependency\Facade\OmsToMailInterface
     */
    protected $mailFacade;

    /**
     * @var \Pyz\Zed\Oms\OmsConfig
     */
    protected $config;

    /**
     * @var \Pyz\Zed\Oms\Business\Model\Durst\DurstCompanyDetailsManagerInterface
     */
    protected $durstCompanyDetailsManager;

    /**
     * InvoiceMailManager constructor.
     *
     * @param \Spryker\Zed\Oms\Dependency\Facade\OmsToMailInterface $mailFacade
     * @param DurstCompanyDetailsManagerInterface $durstCompanyDetailsManager
     * @param \Pyz\Zed\Oms\OmsConfig $config
     */
    public function __construct(
        OmsToMailInterface $mailFacade,
        DurstCompanyDetailsManagerInterface $durstCompanyDetailsManager,
        OmsConfig $config
    ) {
        $this->mailFacade = $mailFacade;
        $this->durstCompanyDetailsManager = $durstCompanyDetailsManager;
        $this->config = $config;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     * @param \Generated\Shared\Transfer\DriverTransfer $driverTransfer
     *
     * @return void
     */
    public function sendMail(OrderTransfer $orderTransfer, BranchTransfer $branchTransfer, DriverTransfer $driverTransfer)
    {
        $mailTransfer = $this
            ->createMailTransfer()
            ->setType(MerchantWholeSaleOrderRefundMailTypePlugin::MAIL_TYPE)
            ->setOrder($orderTransfer)
            ->setBranch($branchTransfer)
            ->setDriver($driverTransfer)
            ->setDurst($this->durstCompanyDetailsManager->createDurstCompanyTransfer())
            ->setFooterBannerImg($this->config->getFooterBannerImg())
            ->setFooterBannerLink($this->config->getFooterBannerLink())
            ->setFooterBannerAlt($this->config->getFooterBannerAlt())
            ->setFooterBannerCta($this->config->getFooterBannerCta())
            ->setBaseUrl($this->config->getBaseUrl())
            ->setMerchantCenterBaseUrl($this->config->getHaendlerBaseUrl());

        $this
            ->mailFacade
            ->handleMail($mailTransfer);
    }

    /**
     * @return \Generated\Shared\Transfer\MailTransfer
     */
    protected function createMailTransfer(): MailTransfer
    {
        return new MailTransfer();
    }
}
