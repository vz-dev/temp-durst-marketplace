<?php
/**
 * Durst - project - CampaignPeriodNotFoundException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 09.06.21
 * Time: 12:01
 */

namespace Pyz\Zed\Campaign\Business\Exception;


class CampaignPeriodNotFoundException extends CampaignPeriodException
{
    public const MESSAGE = 'Es wurde keine Kampagne mit der ID %d gefunden';
}
