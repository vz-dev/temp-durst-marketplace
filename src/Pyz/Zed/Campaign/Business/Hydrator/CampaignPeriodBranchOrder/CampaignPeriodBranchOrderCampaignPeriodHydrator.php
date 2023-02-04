<?php
/**
 * Durst - project - CampaignPeriodBranchOrderCampaignPeriodHydrator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 15.06.21
 * Time: 15:23
 */

namespace Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriodBranchOrder;


use Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer;
use Pyz\Zed\Campaign\Business\CampaignFacadeInterface;

class CampaignPeriodBranchOrderCampaignPeriodHydrator implements CampaignPeriodBranchOrderHydratorInterface
{
    /**
     * @var \Pyz\Zed\Campaign\Business\CampaignFacadeInterface
     */
    protected $facade;

    /**
     * CampaignPeriodBranchOrderCampaignPeriodHydrator constructor.
     * @param \Pyz\Zed\Campaign\Business\CampaignFacadeInterface $facade
     */
    public function __construct(
        CampaignFacadeInterface $facade
    )
    {
        $this->facade = $facade;
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
        $campaignPeriod = $this
            ->facade
            ->getCampaignPeriodById(
                $campaignPeriodBranchOrderTransfer
                    ->getFkCampaignPeriod()
            );

        $campaignPeriodBranchOrderTransfer
            ->setCampaignPeriod(
                $campaignPeriod
            );

        $campaignPeriodBranchOrderTransfer
            ->setEditable(
                $campaignPeriod
                    ->getBookable()
            );
    }
}
