<?php
/**
 * Durst - project - OrderHydrator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-15
 * Time: 16:24
 */

namespace Pyz\Zed\Merchant\Business\Sales;

use Generated\Shared\Transfer\OrderTransfer;
use Pyz\Zed\Merchant\Business\Model\Branch;

class OrderHydrator implements OrderHydratorInterface
{
    /**
     * @var \Pyz\Zed\Merchant\Business\Model\Branch
     */
    protected $branchModel;

    /**
     * OrderHydrator constructor.
     *
     * @param Branch $branchModel
     */
    public function __construct(Branch $branchModel)
    {
        $this->branchModel = $branchModel;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    public function hydrateOrderByBranch(OrderTransfer $orderTransfer): OrderTransfer
    {
        $idBranch = $orderTransfer->requireFkBranch()->getFkBranch();

        return $orderTransfer
            ->setBranch(
                $this->branchModel->getBranchById($idBranch)
            );
    }
}
