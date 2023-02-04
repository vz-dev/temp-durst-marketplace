<?php
/**
 * Durst - project - CancelOrderDriverHydrator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 03.09.21
 * Time: 13:50
 */

namespace Pyz\Zed\CancelOrder\Business\Hydrator;

use Generated\Shared\Transfer\CancelOrderTransfer;
use Pyz\Zed\Driver\Business\DriverFacadeInterface;

/**
 * Class CancelOrderDriverHydrator
 * @package Pyz\Zed\CancelOrder\Business\Hydrator
 */
class CancelOrderDriverHydrator implements CancelOrderHydratorInterface
{
    /**
     * @var \Pyz\Zed\Driver\Business\DriverFacadeInterface
     */
    protected $driverFacade;

    /**
     * @param \Pyz\Zed\Driver\Business\DriverFacadeInterface $driverFacade
     */
    public function __construct(
        DriverFacadeInterface $driverFacade
    )
    {
        $this->driverFacade = $driverFacade;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\CancelOrderTransfer $orderTransfer
     * @return void
     */
    public function hydrateCancelOrder(
        CancelOrderTransfer $orderTransfer
    ): void
    {
        $idDriver = $orderTransfer
            ->getFkDriver();

        if (is_int($idDriver) && $idDriver > 0) {
            $driver = $this
                ->driverFacade
                ->getDriverById(
                    $idDriver
                );

            $orderTransfer
                ->setDriver(
                    $driver
                );
        }
    }
}
