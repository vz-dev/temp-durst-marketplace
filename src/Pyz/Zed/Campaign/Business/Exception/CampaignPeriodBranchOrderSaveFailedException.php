<?php
/**
 * Durst - project - CampaignPeriodBranchOrderSaveFailedException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 26.07.21
 * Time: 15:24
 */

namespace Pyz\Zed\Campaign\Business\Exception;


class CampaignPeriodBranchOrderSaveFailedException extends CampaignPeriodBranchOrderException
{
    public const MESSAGE = 'Beim Speichern der Bestellung ist ein Problem aufgetreten.';
}
