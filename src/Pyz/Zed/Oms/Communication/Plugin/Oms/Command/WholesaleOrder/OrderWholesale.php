<?php
/**
 * Durst - project - OrderWholesale.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 25.07.18
 * Time: 15:18
 */

namespace Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder;

use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\Oms\Communication\OmsCommunicationFactory;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Communication\Plugin\Oms\Command\AbstractCommand;
use Spryker\Zed\Oms\Dependency\Plugin\Command\CommandByOrderInterface;

/**
 * Class OrderWholesale
 * @package Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder
 * @method OmsCommunicationFactory getFactory()
 */
class OrderWholesale extends AbstractCommand implements CommandByOrderInterface
{
    /**
     *
     * Command which is executed per order basis
     *
     * @api
     *
     * @param SpySalesOrderItem[] $orderItems
     * @param SpySalesOrder $orderEntity
     * @param ReadOnlyArrayObject $data
     *
     * @throws PropelException
     *
     * @return array
     */
    public function run(array $orderItems, SpySalesOrder $orderEntity, ReadOnlyArrayObject $data)
    {
        $branchTransfer = $this
            ->getFactory()
            ->getMerchantFacade()
            ->getBranchById(
                $orderEntity
                    ->getFkBranch()
            );

        if ($this
            ->getFactory()
            ->getIntegraFacade()
            ->doesBranchUseIntegra($branchTransfer->getIdBranch()) === true &&
            $orderEntity->getIsExternal() !== true) {
            $orderTransfer = $this
                ->getFactory()
                ->getSalesFacade()
                ->getOrderByIdSalesOrder($orderEntity->getIdSalesOrder());

            $orderTransfer->setIsExportable(true);

            $this
                ->getFactory()
                ->getSalesFacade()
                ->updateOrder($orderTransfer, $orderEntity->getIdSalesOrder());
        }

        if ($branchTransfer->getOrderOnTimeslot() === true) {
            return [];
        }

        if(
            $branchTransfer->getUsesGraphmasters() &&
            ($orderEntity->getGmStartTime() !== null && $orderEntity->getGmEndTime() !== null)
        )
        {
            return [];
        }

        $idConcreteTour = $orderEntity
            ->getSpyConcreteTimeSlot()
            ->getDstConcreteTour()
            ->getIdConcreteTour();

        $this
            ->getFactory()
            ->getTourFacade()
            ->flagConcreteTourForExport($idConcreteTour);

        return [];
    }
}
