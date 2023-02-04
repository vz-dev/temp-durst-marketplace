<?php
/**
 * Durst - project - BranchHydrator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 12.04.21
 * Time: 10:50
 */

namespace Pyz\Zed\Discount\Business\Model\Hydrator\CartDiscountGroup;


use Generated\Shared\Transfer\CartDiscountGroupTransfer;
use Orm\Zed\Discount\Persistence\DstCartDiscountGroup;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;

class BranchHydrator implements CartDiscountGroupHydratorInterface
{
    /**
     * @var \Pyz\Zed\Merchant\Business\MerchantFacadeInterface
     */
    protected $merchantFacade;

    /**
     * BranchHydrator constructor.
     * @param \Pyz\Zed\Merchant\Business\MerchantFacadeInterface $merchantFacade
     */
    public function __construct(
        MerchantFacadeInterface $merchantFacade
    )
    {
        $this->merchantFacade = $merchantFacade;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Orm\Zed\Discount\Persistence\DstCartDiscountGroup $cartDiscountGroup
     * @param \Generated\Shared\Transfer\CartDiscountGroupTransfer $cartDiscountGroupTransfer
     * @return void
     */
    public function hydrateCartDiscountGroup(
        DstCartDiscountGroup $cartDiscountGroup,
        CartDiscountGroupTransfer $cartDiscountGroupTransfer
    ): void
    {
        $branch = $this
            ->merchantFacade
            ->getBranchById(
                $cartDiscountGroup
                    ->getFkBranch()
            );

        $cartDiscountGroupTransfer
            ->setBranch(
                $branch
            );
    }
}
