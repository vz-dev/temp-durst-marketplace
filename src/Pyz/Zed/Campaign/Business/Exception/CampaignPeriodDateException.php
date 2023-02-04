<?php
/**
 * Durst - project - CampaignPeriodDateException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 14.06.21
 * Time: 13:50
 */

namespace Pyz\Zed\Campaign\Business\Exception;


class CampaignPeriodDateException extends CampaignPeriodException
{
    public const MESSAGE_START_DATE = 'Das Startdatum ist ungültig.';
    public const MESSAGE_END_DATE ='Das Enddatum ist ungültig.';
    public const MESSAGE_PERIOD_INVALID = 'Der Zeitraum zwischen %s und %s ist ungültig.';
}
