<?php
/**
 * Durst - project - CampaignPeriodBranchOrderProductDiscountHydrator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 21.06.21
 * Time: 14:17
 */

namespace Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriodBranchOrderProduct;

use Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer;
use Pyz\Zed\Discount\Business\DiscountFacadeInterface;

class CampaignPeriodBranchOrderProductDiscountHydrator implements CampaignPeriodBranchOrderProductHydratorInterface
{
    /**
     * @var \Pyz\Zed\Discount\Business\DiscountFacadeInterface
     */
    protected $discountFacade;

    /**
     * CampaignPeriodBranchOrderProductDiscountHydrator constructor.
     * @param \Pyz\Zed\Discount\Business\DiscountFacadeInterface $discountFacade
     */
    public function __construct(
        DiscountFacadeInterface $discountFacade
    )
    {
        $this->discountFacade = $discountFacade;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
     * @return void
     */
    public function hydrateCampaignPeriodBranchOrderProduct(
        CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
    ): void
    {
        if ($campaignPeriodBranchOrderProductTransfer->getFkDiscount() === null) {
            return;
        }

        $discount = $this
            ->discountFacade
            ->getDiscountConfiguratorTransferById(
                $campaignPeriodBranchOrderProductTransfer
                    ->getFkDiscount()
            );

        if ($discount->getDiscountGeneral()->getIsActive() !== true) {
            $discount = null;
        }

        $campaignPeriodBranchOrderProductTransfer
            ->setDiscount(
                $discount
            );
    }
}
