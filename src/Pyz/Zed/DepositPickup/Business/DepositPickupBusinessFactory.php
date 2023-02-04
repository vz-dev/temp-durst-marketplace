<?php

namespace Pyz\Zed\DepositPickup\Business;

use Pyz\Zed\DepositPickup\Business\Model\DepositPickupInquiry\DepositPickupInquiry;
use Pyz\Zed\DepositPickup\Business\Model\DepositPickupInquiry\DepositPickupInquiryInterface;
use Pyz\Zed\DepositPickup\Business\Model\DepositPickupInquiry\DepositPickupInquirySaver;
use Pyz\Zed\DepositPickup\DepositPickupConfig;
use Pyz\Zed\DepositPickup\DepositPickupDependencyProvider;
use Pyz\Zed\DepositPickup\Persistence\DepositPickupQueryContainer;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Mail\Business\MailFacadeInterface;

/**
 * @method DepositPickupConfig getConfig()
 * @method DepositPickupQueryContainer getQueryContainer()
 */
class DepositPickupBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return DepositPickupInquirySaver
     *
     * @throws ContainerKeyNotFoundException
     */
    public function createDepositPickupInquirySaver(): DepositPickupInquirySaver
    {
        return new DepositPickupInquirySaver(
            $this->getConfig(),
            $this->getMerchantFacade(),
            $this->getMailFacade()
        );
    }

    /**
     * @return DepositPickupInquiryInterface
     *
     * @throws ContainerKeyNotFoundException
     */
    public function createDepositPickupInquiryModel(): DepositPickupInquiryInterface
    {
        return new DepositPickupInquiry(
            $this->getConfig(),
            $this->createDepositPickupInquirySaver(),
            $this->getQueryContainer()
        );
    }

    /**
     * @return MerchantFacadeInterface
     *
     * @throws ContainerKeyNotFoundException
     */
    protected function getMerchantFacade(): MerchantFacadeInterface
    {
        return $this->getProvidedDependency(DepositPickupDependencyProvider::FACADE_MERCHANT);
    }

    /**
     * @return MailFacadeInterface
     *
     * @throws ContainerKeyNotFoundException
     */
    protected function getMailFacade(): MailFacadeInterface
    {
        return $this->getProvidedDependency(DepositPickupDependencyProvider::FACADE_MAIL);
    }
}
