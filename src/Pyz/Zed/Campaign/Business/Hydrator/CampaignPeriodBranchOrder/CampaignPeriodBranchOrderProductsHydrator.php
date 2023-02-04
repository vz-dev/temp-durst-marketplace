<?php
/**
 * Durst - project - CampaignPeriodBranchOrderProductsHydrator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 18.06.21
 * Time: 14:59
 */

namespace Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriodBranchOrder;

use ArrayObject;
use Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer;
use Pyz\Zed\Campaign\Business\CampaignFacadeInterface;

class CampaignPeriodBranchOrderProductsHydrator implements CampaignPeriodBranchOrderHydratorInterface
{
    /**
     * @var \Pyz\Zed\Campaign\Business\CampaignFacadeInterface
     */
    protected $facade;

    /**
     * CampaignPeriodBranchOrderProductsHydrator constructor.
     * @param \Pyz\Zed\Campaign\Business\CampaignFacadeInterface $facade
     */
    public function __construct(
        CampaignFacadeInterface $facade
    )
    {
        $this->facade = $facade;
    }

    /**
     * @inheritDoc
     */
    public function hydrateCampaignPeriodBranchOrder(
        CampaignPeriodBranchOrderTransfer $campaignPeriodBranchOrderTransfer
    ): void
    {
        $products = $this
            ->facade
            ->getProductsForCampaignAndBranch(
                $campaignPeriodBranchOrderTransfer
                    ->getFkCampaignPeriod(),
                $campaignPeriodBranchOrderTransfer
                    ->getFkBranch()
            );

        foreach ($products as $product) {
            $campaignPeriodBranchOrderTransfer
                ->addProduct(
                    $product
                )
                ->addOrderedProduct(
                    $product
                        ->getIdCampaignPeriodBranchOrderProduct()
                );
        }
    }
}
