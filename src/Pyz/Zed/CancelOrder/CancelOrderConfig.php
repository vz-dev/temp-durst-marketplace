<?php
/**
 * Durst - project - CancelOrderConfig.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 30.08.21
 * Time: 17:11
 */

namespace Pyz\Zed\CancelOrder;

use Pyz\Shared\CancelOrder\CancelOrderConstants;
use Pyz\Shared\Mail\MailConstants;
use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;

/**
 * Class CancelOrderConfig
 * @package Pyz\Zed\CancelOrder
 */
class CancelOrderConfig extends AbstractBundleConfig
{
    public const DEFAULT_MAIL_ASSETS_BASE_URL = 'https://haendler.durst.shop';

    /**
     * @return array
     */
    public function getPossibleIssuers(): array
    {
        return $this
            ->get(
                CancelOrderConstants::POSSIBLE_ISSUERS
            );
    }

    /**
     * @return string
     */
    public function getIssuerFridge(): string
    {
        return $this
            ->get(
                CancelOrderConstants::ISSUER_FRIDGE
            );
    }

    /**
     * @return string
     */
    public function getIssuerCustomer(): string
    {
        return $this
            ->get(
                CancelOrderConstants::ISSUER_CUSTOMER
            );
    }

    /**
     * @return string
     */
    public function getIssuerDriver(): string
    {
        return $this
            ->get(
                CancelOrderConstants::ISSUER_DRIVER
            );
    }

    /**
     * @return string
     */
    public function getCancelLeadTime(): string
    {
        return $this
            ->get(
                CancelOrderConstants::CANCEL_LEAD_TIME
            );
    }

    /**
     * @return string
     */
    public function getProjectTimezone(): string
    {
        return $this
            ->get(
                ApplicationConstants::PROJECT_TIMEZONE
            );
    }

    /**
     * @return string
     */
    public function getFridgeCancelUrl(): string
    {
        return $this
            ->get(
                CancelOrderConstants::FRIDGE_CANCEL_URL
            );
    }

    /**
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this
            ->get(
                MailConstants::MAIL_ASSETS_BASE_URL,
                static::DEFAULT_MAIL_ASSETS_BASE_URL
            );
    }

    /**
     * @return string
     */
    public function getFooterBannerImg() : string
    {
        return $this
            ->get(
                MailConstants::MAIL_FOOTER_BANNER_IMG
            );
    }

    /**
     * @return string
     */
    public function getFooterBannerLink() : string
    {
        return $this
            ->get(
                MailConstants::MAIL_FOOTER_BANNER_LINK
            );
    }

    /**
     * @return string
     */
    public function getFooterBannerAlt() : string
    {
        return $this
            ->get(
                MailConstants::MAIL_FOOTER_BANNER_ALT
            );
    }

    /**
     * @return string
     */
    public function getFooterBannerCta() : string
    {
        return $this
            ->get(
                MailConstants::MAIL_FOOTER_BANNER_CTA
            );
    }
}
