<?php
/**
 * Durst - project - IsCustomerValid.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 28.01.20
 * Time: 14:41
 */

namespace Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Condition;

use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Pyz\Zed\HeidelpayRest\Business\HeidelpayRestFacadeInterface;
use Pyz\Zed\HeidelpayRest\Communication\HeidelpayRestCommunicationFactory;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Oms\Dependency\Plugin\Condition\ConditionInterface;

/**
 * Class IsCustomerValid
 * @package Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Condition
 * @method HeidelpayRestCommunicationFactory getFactory()
 * @method HeidelpayRestFacadeInterface getFacade()
 */
class IsCustomerValid extends AbstractPlugin implements ConditionInterface
{
    use IsCustomerValidTrait;

    public const NAME = 'HeidelpayRest/IsCustomerValid';

    /**
     * @inheritDoc
     *
     * @param SpySalesOrderItem $orderItem
     * @return bool
     * @throws ContainerKeyNotFoundException
     */
    public function check(SpySalesOrderItem $orderItem): bool
    {
        return $this->isCustomerValid($orderItem);
    }
}
