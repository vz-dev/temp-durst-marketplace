<?php
/**
 * Durst - project - CampaignAdvertisingMaterialNotFoundException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 09.06.21
 * Time: 13:25
 */

namespace Pyz\Zed\Campaign\Business\Exception;


class CampaignAdvertisingMaterialNotFoundException extends CampaignAdvertisingMaterialException
{
    public const MESSAGE = 'Es wurde kein Werbemittel mit der ID %d für die Kampagne %d gefunden.';
    public const MESSAGE_NO_CAMPAIGN_PERIOD = 'Es wurde kein Werbemittel mit der ID %d gefunden.';
}
