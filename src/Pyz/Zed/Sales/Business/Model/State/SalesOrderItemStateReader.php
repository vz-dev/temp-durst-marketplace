<?php
/**
 * Durst - project - SalesOrderItemState.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 16.03.20
 * Time: 09:51
 */

namespace Pyz\Zed\Sales\Business\Model\State;


use Pyz\Zed\Oms\Persistence\OmsQueryContainerInterface;

class SalesOrderItemStateReader implements SalesOrderItemStateReaderInterface
{
    /**
     * @var \Pyz\Zed\Oms\Persistence\OmsQueryContainerInterface
     */
    protected $omsQueryContainer;

    /**
     * SalesOrderItemState constructor.
     * @param \Pyz\Zed\Oms\Persistence\OmsQueryContainerInterface $omsQueryContainer
     */
    public function __construct(
        OmsQueryContainerInterface $omsQueryContainer
    )
    {
        $this->omsQueryContainer = $omsQueryContainer;
    }

    /**
     * @param array $stateNames
     * @return int[]
     */
    public function getStateIdsByStateNames(array $stateNames): array
    {
        $result = $this
            ->omsQueryContainer
            ->querySalesOrderItemStatesByName(
                $stateNames
            )
            ->find();

        $states = [];

        foreach ($result as $item) {
            $states[] = $item->getIdOmsOrderItemState();
        }

        return $states;
    }
}
