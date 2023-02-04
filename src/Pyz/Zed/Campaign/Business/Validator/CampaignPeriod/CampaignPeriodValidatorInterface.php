<?php
/**
 * Durst - project - CampaignPeriodValidatorInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 14.06.21
 * Time: 10:57
 */

namespace Pyz\Zed\Campaign\Business\Validator\CampaignPeriod;

use Generated\Shared\Transfer\CampaignPeriodTransfer;

interface CampaignPeriodValidatorInterface
{
    /**
     * @param \Generated\Shared\Transfer\CampaignPeriodTransfer $campaignPeriodTransfer
     * @return bool
     */
    public function isValid(
        CampaignPeriodTransfer $campaignPeriodTransfer
    ): bool;
}
