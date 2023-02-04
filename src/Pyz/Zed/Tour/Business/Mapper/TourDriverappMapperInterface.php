<?php
/**
 * Durst - project - TourDriverappMapperInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 10.06.20
 * Time: 15:59
 */

namespace Pyz\Zed\Tour\Business\Mapper;


use Generated\Shared\Transfer\DriverAppTourTransfer;
use Generated\Shared\Transfer\DriverTransfer;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersTour;

interface TourDriverappMapperInterface
{
    /**
     * Please, for love of god only pass eager loaded entities!
     * This method calls a shitload of related entities inside loops, which
     * is a performance nightmare with lazy loaded entities.
     *
     * @param iterable|\Orm\Zed\Tour\Persistence\DstConcreteTour[] $concreteTourEntities
     * @param array $skus the skus of all contained order items so they can be batch loaded
     *
     * @param \Generated\Shared\Transfer\DriverTransfer $driverTransfer
     * @param array $stateWhitelist
     * @return array|\Generated\Shared\Transfer\DriverAppTourTransfer[]
     */
    public function mapEagerLoadedTourEntitiesToTransfers(iterable $concreteTourEntities, array $skus, DriverTransfer $driverTransfer, array $stateWhitelist): array;

    /**
     * Please, for love of god only pass eager loaded entities!
     * This method calls a shitload of related entities inside loops, which
     * is a performance nightmare with lazy loaded entities.
     *
     * @param iterable|DstGraphmastersTour[] $graphmastersTourEntities
     * @param array $skus the skus of all contained order items so they can be batch loaded
     * @param DriverTransfer $driverTransfer
     * @param array $stateWhitelist
     *
     * @return array|DriverAppTourTransfer[]
     */
    public function mapEagerLoadedGraphmastersTourEntitiesToTransfers(
        iterable $graphmastersTourEntities,
        array $skus,
        DriverTransfer $driverTransfer,
        array $stateWhitelist
    ): array;
}
