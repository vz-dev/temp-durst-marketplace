<?php

namespace Pyz\Zed\Campaign\Business\Exception;


class ProductUsedInCampaignException extends CampaignPeriodException
{
    public const MESSAGE = 'Das Produkt wird in aktuellen oder zukünftigen Aktionen oder Kampagnen angeboten und kann nicht gelöscht werden!';
}
