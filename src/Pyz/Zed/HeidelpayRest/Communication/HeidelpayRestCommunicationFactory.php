<?php
/**
 * Durst - project - HeidelpayRestCommunicationFactory.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 31.01.19
 * Time: 14:40
 */

namespace Pyz\Zed\HeidelpayRest\Communication;


use Pyz\Zed\GraphMasters\Business\GraphMastersFacadeInterface;
use Pyz\Zed\Discount\Business\DiscountFacadeInterface;
use Pyz\Zed\HeidelpayRest\Communication\Table\PaymentLogTable;
use Pyz\Zed\HeidelpayRest\Dependency\Facade\HeidelpayRestToOmsBridgeInterface;
use Pyz\Zed\HeidelpayRest\Dependency\Facade\HeidelpayRestToSalesBridgeInterface;
use Pyz\Zed\HeidelpayRest\HeidelpayRestDependencyProvider;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\Touch\Business\TouchFacadeInterface;
use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Mail\Business\MailFacadeInterface;

/**
 * Class HeidelpayRestCommunicationFactory
 * @package Pyz\Zed\HeidelpayRest\Communication
 * @method \Pyz\Zed\HeidelpayRest\Persistence\HeidelpayRestQueryContainerInterface getQueryContainer()
 * @method \Pyz\Zed\HeidelpayRest\HeidelpayRestConfig getConfig()
 */
class HeidelpayRestCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return \Pyz\Zed\HeidelpayRest\Dependency\Facade\HeidelpayRestToSalesBridgeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getSalesFacade(): HeidelpayRestToSalesBridgeInterface
    {
        return $this
            ->getProvidedDependency(HeidelpayRestDependencyProvider::FACADE_SALES);
    }

    /**
     * @return MerchantFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getMerchantFacade(): MerchantFacadeInterface
    {
        return $this
            ->getProvidedDependency(HeidelpayRestDependencyProvider::FACADE_MERCHANT);
    }

    /**
     * @return MailFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getMailFacade(): MailFacadeInterface
    {
        return $this
            ->getProvidedDependency(HeidelpayRestDependencyProvider::FACADE_MAIL);
    }

    /**
     * @return \Pyz\Zed\HeidelpayRest\Communication\Table\PaymentLogTable
     */
    public function createPaymentLogTable(): PaymentLogTable
    {
        return new PaymentLogTable(
            $this->getQueryContainer()
        );
    }

    /**
     * @return TouchFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getTouchFacade() : TouchFacadeInterface
    {
        return $this
            ->getProvidedDependency(HeidelpayRestDependencyProvider::FACADE_TOUCH);
    }

    /**
     * @return DiscountFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getDiscountFacade() : DiscountFacadeInterface
    {
        return $this
            ->getProvidedDependency(HeidelpayRestDependencyProvider::FACADE_DISCOUNT);
    }

    /**
     * @return \Pyz\Zed\HeidelpayRest\Dependency\Facade\HeidelpayRestToOmsBridgeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getOmsFacade(): HeidelpayRestToOmsBridgeInterface
    {
        return $this
            ->getProvidedDependency(
                HeidelpayRestDependencyProvider::FACADE_OMS
            );
    }

    /**
     * @return GraphMastersFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    public function getGraphMastersFacade(): GraphMastersFacadeInterface
    {
        return $this
            ->getProvidedDependency(HeidelpayRestDependencyProvider::FACADE_GRAPHMASTERS);
    }
}
