<?php
/**
 * Durst - project - CampaignPeriodBranchOrderNotFoundException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 21.06.21
 * Time: 16:01
 */

namespace Pyz\Zed\Campaign\Business\Exception;


class CampaignPeriodBranchOrderNotFoundException extends CampaignPeriodBranchOrderException
{
    public const MESSAGE = 'Es wurde keine Bestellung mit der ID "%d" gefunden.';
}
