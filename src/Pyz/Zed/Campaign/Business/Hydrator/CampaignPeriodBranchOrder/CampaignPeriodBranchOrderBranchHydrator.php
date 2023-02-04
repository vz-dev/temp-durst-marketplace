<?php
/**
 * Durst - project - CampaignPeriodBranchOrderBranchHydrator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 15.06.21
 * Time: 15:15
 */

namespace Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriodBranchOrder;


use Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;

class CampaignPeriodBranchOrderBranchHydrator implements CampaignPeriodBranchOrderHydratorInterface
{
    /**
     * @var \Pyz\Zed\Merchant\Business\MerchantFacadeInterface
     */
    protected $merchantFacade;

    /**
     * CampaignPeriodBranchOrderBranchHydrator constructor.
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
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer $campaignPeriodBranchOrderTransfer
     * @return void
     */
    public function hydrateCampaignPeriodBranchOrder(
        CampaignPeriodBranchOrderTransfer $campaignPeriodBranchOrderTransfer
    ): void
    {
        $branch = $this
            ->merchantFacade
            ->getBranchById(
                $campaignPeriodBranchOrderTransfer
                    ->getFkBranch()
            );

        $campaignPeriodBranchOrderTransfer
            ->setBranch(
                $branch
            );
    }
}
