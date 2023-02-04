<?php
/**
 * Durst - project - CampaignConstants.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 24.06.21
 * Time: 09:39
 */

namespace Pyz\Shared\Campaign;


interface CampaignConstants
{
    public const MEDIA_SERVER_HOST = 'MEDIA_SERVER_HOST';
    public const FALLBACK_IMAGE_PRODUCT = 'FALLBACK_IMAGE_PRODUCT';

    public const IMAGE_SCALING_PATH = 'IMAGE_SCALING_PATH';
    public const IMAGE_SCALING_PATH_THUMB = 'IMAGE_SCALING_PATH_THUMB';
    public const IMAGE_SCALING_PATH_BIG = 'IMAGE_SCALING_PATH_BIG';

    public const AVAILABLE_CAMPAIGN_STATUS = 'available';
    public const BOOKED_CAMPAIGN_STATUS = 'booked';

    public const DEEP_LINK_URL = 'DEEP_LINK_URL';

    public const CAMPAIGN_DISCOUNT_NAME = 'CAMPAIGN_DISCOUNT_NAME';
}
