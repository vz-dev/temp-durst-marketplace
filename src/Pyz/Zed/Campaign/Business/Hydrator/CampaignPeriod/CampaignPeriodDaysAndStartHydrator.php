<?php
/**
 * Durst - project - CampaignPeriodWeekAndStartHydrator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 09.06.21
 * Time: 11:21
 */

namespace Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriod;


use DateInterval;
use DateTime;
use Generated\Shared\Transfer\CampaignPeriodTransfer;

class CampaignPeriodDaysAndStartHydrator implements CampaignPeriodHydratorInterface
{
    protected const DATE_INTERVAL_DAY_TEMPLATE = 'P%dD';

    protected const DATE_INTERVAL_DAY_FORMAT = '%d';

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\CampaignPeriodTransfer $campaignPeriodTransfer
     * @return void
     * @throws \Exception
     */
    public function hydrateCampaignPeriod(
        CampaignPeriodTransfer $campaignPeriodTransfer
    ): void
    {
        $campaignDays = new DateInterval(
            sprintf(
                static::DATE_INTERVAL_DAY_TEMPLATE,
                $campaignPeriodTransfer
                    ->getCampaignLeadTime()
            )
        );

        $campaignDays
            ->invert = 1;

        $start = $campaignPeriodTransfer
            ->getCampaignStartDate();

        if (is_string($start)) {
            $start = new DateTime($start);
        }
        $start
            ->setTime(
                0,
                0,
                0
            );

        $end = $campaignPeriodTransfer
            ->getCampaignEndDate();

        if (is_string($end)) {
            $end = new DateTime($end);
        }
        // to include the end day for the duration, the campaign will end the next day at midnight
        $end
            ->setTime(
                0,
                0,
                0
            )
            ->modify(
                '+1 day'
            );

        $duration = $end
            ->diff(
                $start
            );

        $start
            ->add(
                $campaignDays
            );

        $today = new DateTime('now');
        $today
            ->setTime(
                0,
                0,
                0
            );

        $daysDiff = $today
            ->diff(
                $start
            );

        $daysLeft = $daysDiff
            ->days + 1;

        if ($daysDiff->invert === 1) {
            $daysLeft = 0;
        }

        $campaignPeriodTransfer
            ->setDays(
                (int)$campaignDays
                    ->format(
                        static::DATE_INTERVAL_DAY_FORMAT
                    )
            )
            ->setDaysLeft(
                $daysLeft
            )
            ->setCampaignDuration(
                $duration
                    ->days
            )
            ->setCampaignStart(
                $start
            );
    }
}
