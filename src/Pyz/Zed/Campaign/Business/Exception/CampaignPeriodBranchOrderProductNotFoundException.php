<?php
/**
 * Durst - project - CampaignPeriodBranchOrderProductNotFoundException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 29.06.21
 * Time: 10:36
 */

namespace Pyz\Zed\Campaign\Business\Exception;


class CampaignPeriodBranchOrderProductNotFoundException extends CampaignPeriodBranchOrderProductException
{
    public const MESSAGE = 'Das Produkt mit der ID "%d" konnte nicht gefunden werden.';
}
