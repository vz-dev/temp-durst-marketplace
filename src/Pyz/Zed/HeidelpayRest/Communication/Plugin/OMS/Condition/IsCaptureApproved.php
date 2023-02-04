<?php
/**
 * Durst - project - IsCaptureApproved.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 29.01.19
 * Time: 11:09
 */

namespace Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Condition;

use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Pyz\Zed\HeidelpayRest\Business\HeidelpayRestFacadeInterface;
use Pyz\Zed\HeidelpayRest\Communication\HeidelpayRestCommunicationFactory;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Oms\Dependency\Plugin\Condition\ConditionInterface;

/**
 * Class IsCaptureApproved
 * @package Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Condition
 * @method HeidelpayRestFacadeInterface getFacade()
 * @method HeidelpayRestCommunicationFactory getFactory()
 */
class IsCaptureApproved extends AbstractPlugin implements ConditionInterface
{
    use IsCaptureApprovedTrait;

    public const NAME = 'HeidelpayRest/IsCaptureApproved';

    /**
     * @api
     *
     * @param SpySalesOrderItem $orderItem
     * @return bool
     * @throws ContainerKeyNotFoundException
     */
    public function check(SpySalesOrderItem $orderItem): bool
    {
        return $this->isCaptureApproved($orderItem);
    }
}
