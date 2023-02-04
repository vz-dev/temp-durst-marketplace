<?php
/**
 * Durst - project - CampaignPeriodDateValidator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 14.06.21
 * Time: 10:58
 */

namespace Pyz\Zed\Campaign\Business\Validator\CampaignPeriod;

use DateTime;
use Generated\Shared\Transfer\CampaignPeriodTransfer;
use Propel\Runtime\ActiveQuery\Criteria;
use Pyz\Zed\Campaign\Business\Exception\CampaignPeriodDateException;
use Pyz\Zed\Campaign\Business\Validator\BaseCampaignValidator;

class CampaignPeriodDateValidator extends BaseCampaignValidator implements CampaignPeriodValidatorInterface
{
    protected const DATETIME_FORMAT = 'd.m.Y';

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\CampaignPeriodTransfer $campaignPeriodTransfer
     * @return bool
     * @throws \Pyz\Zed\Campaign\Business\Exception\CampaignPeriodDateException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function isValid(
        CampaignPeriodTransfer $campaignPeriodTransfer
    ): bool
    {
        $start = $campaignPeriodTransfer
            ->getCampaignStartDate();

        $end = $campaignPeriodTransfer
            ->getCampaignEndDate();

        $periodsQuery = $this
            ->queryContainer
            ->queryCampaignPeriod()
            ->condition(
                'condition1',
                'DstCampaignPeriod.CampaignStartDate <= ?',
                $start
            )
            ->condition(
                'condition2',
                'DstCampaignPeriod.CampaignEndDate >= ?',
                $start
            )
            ->where(
                [
                    'condition1',
                    'condition2'
                ],
                Criteria::LOGICAL_AND
            )
            ->condition(
                'condition3',
                'DstCampaignPeriod.CampaignStartDate <= ?',
                $end
            )
            ->condition(
                'condition4',
                'DstCampaignPeriod.CampaignEndDate >= ?',
                $end
            )
            ->_or()
            ->where(
                [
                    'condition3',
                    'condition4'
                ],
                Criteria::LOGICAL_AND
            )
            ->condition(
                'condition5',
                'DstCampaignPeriod.CampaignStartDate >= ?',
                $start
            )
            ->condition(
                'condition6',
                'DstCampaignPeriod.CampaignEndDate <= ?',
                $end
            )
            ->_or()
            ->where(
                [
                    'condition5',
                    'condition6'
                ],
                Criteria::LOGICAL_AND
            )
            ->filterByIsActive(
                true
            );

        if ($campaignPeriodTransfer->getIdCampaignPeriod() !== null) {
            $periodsQuery = $periodsQuery
                ->filterByIdCampaignPeriod(
                    $campaignPeriodTransfer
                        ->getIdCampaignPeriod(),
                    Criteria::NOT_EQUAL
                );
        }

        $periodsCount = $periodsQuery
            ->count();

        if ($periodsCount !== 0) {
            if (is_string($start)) {
                $start = new DateTime($start);
            }

            if (is_string($end)) {
                $end = new DateTime($end);
            }

            throw new CampaignPeriodDateException(
                sprintf(
                    CampaignPeriodDateException::MESSAGE_PERIOD_INVALID,
                    $start
                        ->format(
                            static::DATETIME_FORMAT
                        ),
                    $end
                        ->format(
                            static::DATETIME_FORMAT
                        )
                )
            );
        }

        return true;
    }
}
