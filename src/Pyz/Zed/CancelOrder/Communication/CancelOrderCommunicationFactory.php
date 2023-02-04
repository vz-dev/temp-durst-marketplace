<?php
/**
 * Durst - project - CancelOrderCommunicationFactory.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 31.08.21
 * Time: 09:30
 */

namespace Pyz\Zed\CancelOrder\Communication;

use Pyz\Zed\Auth\Business\AuthFacadeInterface;
use Pyz\Zed\CancelOrder\Business\CancelOrderFacadeInterface;
use Pyz\Zed\CancelOrder\CancelOrderConfig;
use Pyz\Zed\CancelOrder\CancelOrderDependencyProvider;
use Pyz\Zed\CancelOrder\Communication\Table\CancelOrderTable;
use Pyz\Zed\CancelOrder\Persistence\CancelOrderQueryContainerInterface;
use Pyz\Zed\Edifact\Business\EdifactFacadeInterface;
use Pyz\Zed\HeidelpayRest\Business\HeidelpayRestFacadeInterface;
use Pyz\Zed\Integra\Business\IntegraFacadeInterface;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\Oms\Business\OmsFacadeInterface;
use Pyz\Zed\Oms\Persistence\OmsQueryContainerInterface;
use Pyz\Zed\Sales\Business\SalesFacadeInterface;
use Pyz\Zed\Touch\Business\TouchFacadeInterface;
use Pyz\Zed\Tour\Business\TourFacadeInterface;
use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use Spryker\Zed\Mail\Business\MailFacadeInterface;
use Spryker\Zed\StateMachine\Business\StateMachineFacadeInterface;

/**
 * Class CancelOrderCommunicationFactory
 * @package Pyz\Zed\CancelOrder\Communication
 *
 * @method CancelOrderConfig getConfig()
 */
class CancelOrderCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return \Pyz\Zed\CancelOrder\Communication\Table\CancelOrderTable
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createCancelOrderTable(): CancelOrderTable
    {
        return new CancelOrderTable(
            $this
                ->getCancelOrderQueryContainer(),
            $this
                ->getCancelOrderFacade(),
            $this
                ->getConfig()
        );
    }

    /**
     * @return \Pyz\Zed\CancelOrder\Business\CancelOrderFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getCancelOrderFacade(): CancelOrderFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                CancelOrderDependencyProvider::FACADE_CANCEL_ORDER
            );
    }

    /**
     * @return \Pyz\Zed\Oms\Business\OmsFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getOmsFacade(): OmsFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                CancelOrderDependencyProvider::FACADE_OMS
            );
    }

    /**
     * @return \Pyz\Zed\Sales\Business\SalesFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getSalesFacade(): SalesFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                CancelOrderDependencyProvider::FACADE_SALES
            );
    }

    /**
     * @return \Pyz\Zed\Tour\Business\TourFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getTourFacade(): TourFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                CancelOrderDependencyProvider::FACADE_TOUR
            );
    }

    /**
     * @return \Pyz\Zed\Touch\Business\TouchFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getTouchFacade(): TouchFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                CancelOrderDependencyProvider::FACADE_TOUCH
            );
    }

    /**
     * @return \Pyz\Zed\HeidelpayRest\Business\HeidelpayRestFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getHeidelpayRestFacade(): HeidelpayRestFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                CancelOrderDependencyProvider::FACADE_HEIDELPAY_REST
            );
    }

    /**
     * @return \Spryker\Zed\Mail\Business\MailFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getMailFacade(): MailFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                CancelOrderDependencyProvider::FACADE_MAIL
            );
    }

    /**
     * @return \Pyz\Zed\Merchant\Business\MerchantFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getMerchantFacade(): MerchantFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                CancelOrderDependencyProvider::FACADE_MERCHANT
            );
    }

    /**
     * @return \Pyz\Zed\Auth\Business\AuthFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getAuthFacade(): AuthFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                CancelOrderDependencyProvider::FACADE_AUTH
            );
    }

    /**
     * @return \Pyz\Zed\Integra\Business\IntegraFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getIntegraFacade(): IntegraFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                CancelOrderDependencyProvider::FACADE_INTEGRA
            );
    }

    /**
     * @return \Spryker\Zed\StateMachine\Business\StateMachineFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getStateMachineFacade(): StateMachineFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                CancelOrderDependencyProvider::FACADE_STATE_MACHINE
            );
    }

    /**
     * @return \Pyz\Zed\Edifact\Business\EdifactFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getEdifactFacade(): EdifactFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                CancelOrderDependencyProvider::FACADE_EDIFACT
            );
    }

    /**
     * @return \Pyz\Zed\CancelOrder\Persistence\CancelOrderQueryContainerInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getCancelOrderQueryContainer(): CancelOrderQueryContainerInterface
    {
        return $this
            ->getProvidedDependency(
                CancelOrderDependencyProvider::QUERY_CONTYINER_CANCEL_ORDER
            );
    }

    /**
     * @return \Pyz\Zed\Oms\Persistence\OmsQueryContainerInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getOmsQueryContainer(): OmsQueryContainerInterface
    {
        return $this
            ->getProvidedDependency(
                CancelOrderDependencyProvider::QUERY_CONTAINER_OMS
            );
    }
}
