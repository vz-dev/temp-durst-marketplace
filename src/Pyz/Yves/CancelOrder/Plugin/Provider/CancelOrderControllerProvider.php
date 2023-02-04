<?php
/**
 * Durst - project - CancelOrderControllerProvider.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 14.09.21
 * Time: 11:48
 */

namespace Pyz\Yves\CancelOrder\Plugin\Provider;

use Pyz\Yves\Application\Plugin\Provider\AbstractYvesControllerProvider;
use Silex\Application;

class CancelOrderControllerProvider extends AbstractYvesControllerProvider
{
    public const ROUTE_CANCEL_ORDER_CANCEL = 'cancel-order/cancel';
    public const ROUTE_CANCEL_ORDER_FAILED = 'cancel-order/fail';
    public const ROUTE_CANCEL_ORDER_SUCCESS = 'cancel-order/success';

    /**
     * {@inheritDoc}
     *
     * @param \Silex\Application $app
     * @return void
     */
    protected function defineControllers(
        Application $app
    ): void
    {
        $allowedLocalesPattern = $this->getAllowedLocalesPattern();

        $this
            ->createController(
                '/cancel-order/cancel',
                self::ROUTE_CANCEL_ORDER_CANCEL,
                'CancelOrder',
                'Cancel',
                'cancel'
            )
            ->assert(
                'cancel-order',
                $allowedLocalesPattern . 'cancel-order|cancel-order'
            );

        $this
            ->createController(
                '/cancel-order/fail',
                self::ROUTE_CANCEL_ORDER_FAILED,
                'CancelOrder',
                'Cancel',
                'fail'
            )
            ->assert(
                'cancel-order',
                $allowedLocalesPattern . 'cancel-order|cancel-order'
            );

        $this
            ->createController(
                '/cancel-order/success',
                self::ROUTE_CANCEL_ORDER_SUCCESS,
                'CancelOrder',
                'Cancel',
                'success'
            )
            ->assert(
                'cancel-order',
                $allowedLocalesPattern . 'cancel-order|cancel-order'
            );
    }
}
