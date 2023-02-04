<?php
/**
 * Durst - project - SaveCancellation.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 13.09.21
 * Time: 23:53
 */

namespace Pyz\Zed\CancelOrder\Communication\Plugin\OMS\Command;

use DateTime;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Pyz\Zed\CancelOrder\Business\CancelOrderFacadeInterface;
use Pyz\Zed\CancelOrder\CancelOrderConfig;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Communication\Plugin\Oms\Command\AbstractCommand;
use Spryker\Zed\Oms\Dependency\Plugin\Command\CommandByOrderInterface;

/**
 * Class SaveCancellation
 * @package Pyz\Zed\CancelOrder\Communication\Plugin\OMS\Command
 *
 * @method CancelOrderFacadeInterface getFacade()
 * @method CancelOrderConfig getConfig()
 */
class SaveCancellation extends AbstractCommand implements CommandByOrderInterface
{
    public const EVENT_ID = 'saveCancellation';
    public const NAME = 'CancelOrder/SaveCancellation';
    public const STATE_NAME = 'persist cancellation';

    /**
     * {@inheritDoc}
     *
     * @param array $orderItems
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $orderEntity
     * @param \Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject $data
     * @return array
     */
    public function run(
        array $orderItems,
        SpySalesOrder $orderEntity,
        ReadOnlyArrayObject $data
    ): array
    {
        $manualExpireDate = null;

        if ($orderEntity->getCancelIssuer() === $this->getConfig()->getIssuerDriver()) {
            $manualExpireDate = new DateTime('tomorrow midnight');
        }

        $token = $this
            ->getFacade()
            ->generateToken(
                $orderEntity
                    ->getIdSalesOrder(),
                $manualExpireDate
            );

        $this
            ->getFacade()
            ->saveCancelOrder(
                $token
                    ->getToken()
            );

        return [];
    }
}
