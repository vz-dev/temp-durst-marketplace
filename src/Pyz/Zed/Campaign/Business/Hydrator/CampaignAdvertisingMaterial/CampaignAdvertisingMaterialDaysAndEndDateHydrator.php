<?php
/**
 * Durst - project - CampaignAdvertisingMaterialCampaignPeriodHydrator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 09.06.21
 * Time: 11:34
 */

namespace Pyz\Zed\Campaign\Business\Hydrator\CampaignAdvertisingMaterial;

use DateInterval;
use DateTime;
use Generated\Shared\Transfer\CampaignAdvertisingMaterialTransfer;
use Pyz\Zed\Campaign\Persistence\CampaignQueryContainerInterface;

class CampaignAdvertisingMaterialDaysAndEndDateHydrator implements CampaignAdvertisingMaterialHydratorInterface
{
    protected const DATE_INTERVAL_WEEK_TEMPLATE = 'P%dW';
    protected const DATE_INTERVAL_DAY_TEMPLATE = 'P%dD';

    protected const DATE_INTERVAL_DAY_FORMAT = '%d';

    /**
     * @var \Pyz\Zed\Campaign\Persistence\CampaignQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * CampaignAdvertisingMaterialDaysAndDatesHydrator constructor.
     * @param \Pyz\Zed\Campaign\Persistence\CampaignQueryContainerInterface $queryContainer
     */
    public function __construct(
        CampaignQueryContainerInterface $queryContainer
    )
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\CampaignAdvertisingMaterialTransfer $advertisingMaterialTransfer
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function hydrateCampaignAdvertisingMaterial(
        CampaignAdvertisingMaterialTransfer $advertisingMaterialTransfer
    ): void
    {
        if ($advertisingMaterialTransfer->getFkCampaignPeriod() === null) {
            return;
        }

        $campaignPeriod = $this
            ->queryContainer
            ->queryCampaignPeriod()
            ->useDstCampaignPeriodCampaignAdvertisingMaterialQuery()
                ->filterByIdCampaignPeriod(
                    $advertisingMaterialTransfer
                        ->getFkCampaignPeriod()
                )
                ->filterByIdCampaignAdvertisingMaterial(
                    $advertisingMaterialTransfer
                        ->getIdCampaignAdvertisingMaterial()
                )
            ->endUse()
            ->findOne();

        $materialWeeks = new DateInterval(
            sprintf(
                static::DATE_INTERVAL_WEEK_TEMPLATE,
                $advertisingMaterialTransfer
                    ->getCampaignAdvertisingMaterialLeadTime()
            )
        );

        $materialWeeks
            ->invert = 1;

        $periodDays = new DateInterval(
            sprintf(
                static::DATE_INTERVAL_DAY_TEMPLATE,
                $campaignPeriod
                    ->getCampaignLeadTime()
            )
        );

        $periodDays
            ->invert = 1;

        $end = clone $campaignPeriod
            ->getCampaignStartDate();

        if (is_string($end)) {
            $end = new DateTime($end);
        }

        $end
            ->add(
                $periodDays
            )
            ->add(
                $materialWeeks
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
                $end
            );

        $daysLeft = $daysDiff
            ->days + 1;

        if ($daysDiff->invert === 1) {
            $daysLeft = 0;
        }

        $advertisingMaterialTransfer
            ->setDays(
                (int)$materialWeeks
                    ->format(
                        static::DATE_INTERVAL_DAY_FORMAT
                    )
            )
            ->setDaysLeft(
                $daysLeft
            )
            ->setCampaignAdvertisingMaterialEndDate(
                $end
            );
    }
}
