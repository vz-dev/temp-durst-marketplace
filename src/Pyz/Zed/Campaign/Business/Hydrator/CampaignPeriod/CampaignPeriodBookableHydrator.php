<?php
/**
 * Durst - project - CampaignPeriodBookableHydrator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 22.06.21
 * Time: 09:10
 */

namespace Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriod;


use Generated\Shared\Transfer\CampaignPeriodTransfer;

class CampaignPeriodBookableHydrator implements CampaignPeriodHydratorInterface
{
    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\CampaignPeriodTransfer $campaignPeriodTransfer
     * @return void
     */
    public function hydrateCampaignPeriod(
        CampaignPeriodTransfer $campaignPeriodTransfer
    ): void
    {
        $bookable = $campaignPeriodTransfer
            ->getIsActive();

        if ($bookable === true) {
            $daysLeft = array_map(
                function ($material) {
                    return $material->getDaysLeft();
                },
                $campaignPeriodTransfer
                    ->getAssignedCampaignAdvertisingMaterials()
                    ->getArrayCopy()
            );

            $waitingTimes = array_filter(
                $daysLeft
            );

            $bookable = (count($waitingTimes) > 0);
        }

        $campaignPeriodTransfer
            ->setBookable(
                $bookable
            );
    }
}
