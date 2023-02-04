<?php
/**
 * Durst - project - CampaignConfig.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 09.06.21
 * Time: 11:41
 */

namespace Pyz\Zed\Campaign;

use Pyz\Shared\Campaign\CampaignConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;

class CampaignConfig extends AbstractBundleConfig
{
    /**
     * @return string
     */
    public function getMediaServerHost(): string
    {
        return $this
            ->get(
                CampaignConstants::MEDIA_SERVER_HOST
            );
    }

    /**
     * @return string
     */
    public function getFallbackImageProduct(): string
    {
        return $this
            ->get(
                CampaignConstants::FALLBACK_IMAGE_PRODUCT
            );
    }

    /**
     * @return string
     */
    public function getThumbImageHost(): string
    {
        return sprintf(
            '%s/%s',
            $this
                ->getMediaServerHost(),
            $this
                ->get(
                    CampaignConstants::IMAGE_SCALING_PATH_THUMB
                )
        );
    }

    /**
     * @return string
     */
    public function getBigImageHost(): string
    {
        return sprintf(
            '%s/%s',
            $this
                ->getMediaServerHost(),
            $this
                ->get(
                    CampaignConstants::IMAGE_SCALING_PATH_BIG
                )
        );
    }

    /**
     * @param string $branchCode
     * @param string $abstractSku
     * @return string
     */
    public function getDeepLinkUrl(
        string $branchCode,
        string $abstractSku
    ): string
    {
        return sprintf(
            $this
                ->get(
                    CampaignConstants::DEEP_LINK_URL
                ),
            $branchCode,
            $abstractSku
        );
    }

    /**
     * @return string
     */
    public function getDiscountName(): string
    {
        return $this
            ->get(
                CampaignConstants::CAMPAIGN_DISCOUNT_NAME
            );
    }
}
