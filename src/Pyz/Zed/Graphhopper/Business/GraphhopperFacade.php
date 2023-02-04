<?php
/**
 * Durst - project - GraphhopperFacade.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 27.11.19
 * Time: 16:07
 */

namespace Pyz\Zed\Graphhopper\Business;


use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\GraphhopperCoordinatesTransfer;
use Generated\Shared\Transfer\GraphhopperTourTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * Class GraphhopperFacade
 * @package Pyz\Zed\Graphhopper\Business
 * @method GraphhopperBusinessFactory getFactory()
 */
class GraphhopperFacade extends AbstractFacade implements GraphhopperFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @param string $queryString
     * @return GraphhopperCoordinatesTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getCoordinatesForAddressString(string $queryString): GraphhopperCoordinatesTransfer
    {
        return $this
            ->getFactory()
            ->createGeocoding()
            ->getCoordinatesForAddressString($queryString);
    }

    /**
     * {@inheritdoc}
     *
     * @param GraphhopperTourTransfer $graphhopperTourTransfer
     * @return GraphhopperTourTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function orderTourOrders(GraphhopperTourTransfer $graphhopperTourTransfer): GraphhopperTourTransfer
    {
        return $this
            ->getFactory()
            ->createTourOrderSorter()
            ->orderTourOrders($graphhopperTourTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param AddressTransfer $addressTransfer
     * @param SaveOrderTransfer $saveOrderTransfer
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function saveLatLngInOrderAddress(AddressTransfer $addressTransfer, SaveOrderTransfer $saveOrderTransfer){
        $this
            ->getFactory()
            ->createLatLngAddressSaver()
            ->saveLatLngToAddress($addressTransfer, $saveOrderTransfer);
    }
}
