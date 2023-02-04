<?php
/**
 * Durst - project - CampaignPeriodBranchOrderProductBranchHydrator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 21.06.21
 * Time: 14:33
 */

namespace Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriodBranchOrderProduct;

use Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;

class CampaignPeriodBranchOrderProductBranchHydrator implements CampaignPeriodBranchOrderProductHydratorInterface
{
    /**
     * @var \Pyz\Zed\Merchant\Business\MerchantFacadeInterface
     */
    protected $merchantFacade;

    /**
     * CampaignPeriodBranchOrderProductBranchHydrator constructor.
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
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
     * @return void
     */
    public function hydrateCampaignPeriodBranchOrderProduct(
        CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
    ): void
    {
        $branch = $this
            ->merchantFacade
            ->getBranchById(
                $campaignPeriodBranchOrderProductTransfer
                    ->getFkBranch()
            );

        $campaignPeriodBranchOrderProductTransfer
            ->setBranch(
                $branch
            );
    }
}
