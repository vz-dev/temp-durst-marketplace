<?php
/**
 * Durst - project - CancelOrderBusinessFactory.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 30.08.21
 * Time: 16:48
 */

namespace Pyz\Zed\CancelOrder\Business;

use Pyz\Zed\CancelOrder\Business\Hydrator\CancelOrderConcreteTourHydrator;
use Pyz\Zed\CancelOrder\Business\Hydrator\CancelOrderDriverHydrator;
use Pyz\Zed\CancelOrder\Business\Hydrator\CancelOrderHydratorInterface;
use Pyz\Zed\CancelOrder\Business\Hydrator\CancelOrderJwtHydrator;
use Pyz\Zed\CancelOrder\Business\Hydrator\CancelOrderSalesOrderHydrator;
use Pyz\Zed\CancelOrder\Business\Manager\CancelOrderManager;
use Pyz\Zed\CancelOrder\Business\Manager\CancelOrderManagerInterface;
use Pyz\Zed\CancelOrder\Business\Model\CancelOrder;
use Pyz\Zed\CancelOrder\Business\Model\CancelOrderInterface;
use Pyz\Zed\CancelOrder\Business\Validator\CancelOrderExistsValidator;
use Pyz\Zed\CancelOrder\CancelOrderConfig;
use Pyz\Zed\CancelOrder\CancelOrderDependencyProvider;
use Pyz\Zed\CancelOrder\Persistence\CancelOrderQueryContainerInterface;
use Pyz\Zed\Driver\Business\DriverFacadeInterface;
use Pyz\Zed\Jwt\Business\JwtFacadeInterface;
use Pyz\Zed\Jwt\Business\Validator\JwtValidatorInterface;
use Pyz\Zed\Sales\Business\SalesFacadeInterface;
use Pyz\Zed\Tour\Business\TourFacadeInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;

/**
 * Class CancelOrderBusinessFactory
 * @package Pyz\Zed\CancelOrder\Business
 *
 * @method CancelOrderConfig getConfig()
 * @method CancelOrderQueryContainerInterface getQueryContainer()
 */
class CancelOrderBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \Pyz\Zed\CancelOrder\Business\Model\CancelOrderInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createCancelOrderModel(): CancelOrderInterface
    {
        return new CancelOrder(
            $this
                ->getJwtFacade(),
            $this
                ->getSalesFacade(),
            $this
                ->getTourFacade(),
            $this
                ->getQueryContainer(),
            $this
                ->getConfig(),
            $this
                ->getCancelOrderHydrators(),
            $this
                ->getCancelOrdersValidators()
        );
    }

    /**
     * @return \Pyz\Zed\CancelOrder\Business\Manager\CancelOrderManagerInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createCancelOrderManager(): CancelOrderManagerInterface
    {
        return new CancelOrderManager(
            $this
                ->getCancelOrderFacade(),
            $this
                ->getSalesFacade()
        );
    }

    /**
     * @return \Pyz\Zed\Jwt\Business\Validator\JwtValidatorInterface[]
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getCancelOrdersValidators(): array
    {
        return [
            $this
                ->getCancelOrderExistsValidator()
        ];
    }

    /**
     * @return \Pyz\Zed\CancelOrder\Business\Hydrator\CancelOrderHydratorInterface[]
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getCancelOrderHydrators(): array
    {
        return [
            $this
                ->getCancelOrderSalesOrderHydrator(),
            $this
                ->getCancelOrderConcreteTourHydrator(),
            $this
                ->getCancelOrderJwtHydrator(),
            $this
                ->getCancelOrderDriverHydrator()
        ];
    }

    /**
     * @return \Pyz\Zed\Jwt\Business\Validator\JwtValidatorInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getCancelOrderExistsValidator(): JwtValidatorInterface
    {
        return new CancelOrderExistsValidator(
            $this
                ->getCancelOrderFacade()
        );
    }

    /**
     * @return \Pyz\Zed\CancelOrder\Business\Hydrator\CancelOrderHydratorInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getCancelOrderSalesOrderHydrator(): CancelOrderHydratorInterface
    {
        return new CancelOrderSalesOrderHydrator(
            $this
                ->getSalesFacade()
        );
    }

    /**
     * @return \Pyz\Zed\CancelOrder\Business\Hydrator\CancelOrderHydratorInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getCancelOrderConcreteTourHydrator(): CancelOrderHydratorInterface
    {
        return new CancelOrderConcreteTourHydrator(
            $this
                ->getTourFacade()
        );
    }

    /**
     * @return \Pyz\Zed\CancelOrder\Business\Hydrator\CancelOrderHydratorInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getCancelOrderJwtHydrator(): CancelOrderHydratorInterface
    {
        return new CancelOrderJwtHydrator(
            $this
                ->getCancelOrderFacade()
        );
    }

    /**
     * @return \Pyz\Zed\CancelOrder\Business\Hydrator\CancelOrderHydratorInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getCancelOrderDriverHydrator(): CancelOrderHydratorInterface
    {
        return new CancelOrderDriverHydrator(
            $this
                ->getDriverFacade()
        );
    }

    /**
     * @return \Pyz\Zed\Jwt\Business\JwtFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getJwtFacade(): JwtFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                CancelOrderDependencyProvider::FACADE_JWT
            );
    }

    /**
     * @return \Pyz\Zed\Sales\Business\SalesFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getSalesFacade(): SalesFacadeInterface
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
    protected function getTourFacade(): TourFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                CancelOrderDependencyProvider::FACADE_TOUR
            );
    }

    /**
     * @return \Pyz\Zed\CancelOrder\Business\CancelOrderFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getCancelOrderFacade(): CancelOrderFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                CancelOrderDependencyProvider::FACADE_CANCEL_ORDER
            );
    }

    /**
     * @return \Pyz\Zed\Driver\Business\DriverFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getDriverFacade(): DriverFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                CancelOrderDependencyProvider::FACADE_DRIVER
            );
    }
}
