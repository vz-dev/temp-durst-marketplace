<?php
/**
 * Durst - project - CancelOrderJwtHydrator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 31.08.21
 * Time: 15:49
 */

namespace Pyz\Zed\CancelOrder\Business\Hydrator;

use Generated\Shared\Transfer\CancelOrderTransfer;
use Pyz\Zed\CancelOrder\Business\CancelOrderFacadeInterface;

/**
 * Class CancelOrderJwtHydrator
 * @package Pyz\Zed\CancelOrder\Business\Hydrator
 */
class CancelOrderJwtHydrator implements CancelOrderHydratorInterface
{
    /**
     * @var \Pyz\Zed\CancelOrder\Business\CancelOrderFacadeInterface
     */
    protected $facade;

    /**
     * @param \Pyz\Zed\CancelOrder\Business\CancelOrderFacadeInterface $facade
     */
    public function __construct(
        CancelOrderFacadeInterface $facade
    )
    {
        $this->facade = $facade;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\CancelOrderTransfer $orderTransfer
     * @return void
     */
    public function hydrateCancelOrder(CancelOrderTransfer $orderTransfer): void
    {
        $jwt = $this
            ->facade
            ->getJwtFromToken(
                $orderTransfer
                    ->getToken()
            );

        $orderTransfer
            ->setJwt(
                $jwt
            );
    }
}
