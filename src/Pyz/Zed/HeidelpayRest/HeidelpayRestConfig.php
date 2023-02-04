<?php
/**
 * Durst - project - HeidelpayRestConfig.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 15.01.19
 * Time: 15:10
 */

namespace Pyz\Zed\HeidelpayRest;

use Pyz\Shared\HeidelpayRest\HeidelpayRestConstants;
use Pyz\Shared\Mail\MailConstants;
use Pyz\Shared\Oms\OmsConstants;
use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;

class HeidelpayRestConfig extends AbstractBundleConfig
{
    public const DEFAULT_IS_DEBUG = true;
    public const DEFAULT_LOCALE = 'de_DE';
    public const DEFAULT_FALLBACK_ERROR_MESSAGE = 'Die Zahlungsautorisierung wurde leider abgelehnt';

    public const ERROR_CODE = 5001;

    /**
     * @return bool
     */
    public function getIsDebug(): bool
    {
        return $this
            ->get(HeidelpayRestConstants::HEIDELPAY_REST_IS_DEBUG, static::DEFAULT_IS_DEBUG);
    }

    /**
     * @return string
     */
    public function getPublicKey(): string
    {
        return $this
            ->get(HeidelpayRestConstants::HEIDELPAY_REST_PUBLIC_KEY);
    }

    /**
     * @return string
     */
    public function getPrvateKey(): string
    {
        return $this
            ->get(HeidelpayRestConstants::HEIDELPAY_REST_PRIVATE_KEY);
    }

    /**
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this
            ->get(HeidelpayRestConstants::HEIDELPAY_BASE_URL);
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this
            ->get(HeidelpayRestConstants::HEIDELPAY_LOCALE, static::DEFAULT_LOCALE);
    }

    /**
     * @return string
     */
    public function getReturnUrl(): string
    {
        return $this
            ->get(HeidelpayRestConstants::HEIDELPAY_RETURN_URL);
    }

    /**
     * @return string
     */
    public function getDebugLogPath(): string
    {
        return $this
            ->get(HeidelpayRestConstants::HEIDELPAY_REST_DEBUG_LOG_PATH);
    }

    /**
     * @return string
     */
    public function getMailBaseUrl(): string
    {
        return $this
            ->get(MailConstants::MAIL_ASSETS_BASE_URL);
    }

    /**
     * @return string
     */
    public function getPaymentFeedbackUrl(): string
    {
        return $this
            ->get(MailConstants::MAIL_CUSTOMER_PAYMENT_FEEDBACK_URL);
    }

    /**
     * @return string
     */
    public function getSepaMandateUrl(): string
    {
        return $this
            ->get(HeidelpayRestConstants::HEIDELPAY_REST_SEPA_MANDATE_URL);
    }

    /**
     * @return string
     */
    public function getFallbackErrorMessage(): string
    {
        return $this
            ->get(
                HeidelpayRestConstants::HEIDELPAY_REST_FALLBACK_ERROR_MESSAGE,
                static::DEFAULT_FALLBACK_ERROR_MESSAGE
            );
    }

    /**
     * @return string
     */
    public function getHeidelPayStartDateBranchSpecificKeys(): string
    {
        return $this
            ->get(HeidelpayRestConstants::HEIDELPAY_REST_START_DATE_BRANCH_SPECIFIC_KEYS);
    }

    /**
     * @return string
     */
    public function getFooterBannerImg() : string
    {
        return $this
            ->get(MailConstants::MAIL_FOOTER_BANNER_IMG);
    }

    /**
     * @return string
     */
    public function getFooterBannerLink() : string
    {
        return $this
            ->get(MailConstants::MAIL_FOOTER_BANNER_LINK);
    }

    /**
     * @return string
     */
    public function getFooterBannerAlt() : string
    {
        return $this
            ->get(MailConstants::MAIL_FOOTER_BANNER_ALT);
    }

    /**
     * @return string
     */
    public function getFooterBannerCta() : string
    {
        return $this
            ->get(MailConstants::MAIL_FOOTER_BANNER_CTA);
    }

    /**
     * @return string
     */
    public function getAssetBaseUrl(): string
    {
        return $this
            ->get(MailConstants::MAIL_ASSETS_BASE_URL);
    }

    /**
     * @return int
     */
    public function getSalesOrderRetryCounter(): int
    {
        return $this
            ->get(
                OmsConstants::SALES_ORDER_RETRY_COUNTER
            );
    }

    /**
     * @return array
     */
    public function getOmsErrorMailRecipients(): array
    {
        return $this
            ->get(
                OmsConstants::OMS_ERROR_MAIL_RECIPIENTS
            );
    }

    /**
     * @return string
     */
    public function getOmsErrorMailSubject(): string
    {
        return $this
            ->get(
                OmsConstants::OMS_ERROR_MAIL_SUBJECT
            );
    }

    /**
     * @return array
     */
    public function getHeidelpayRestPaymentTypeMap(): array
    {
        return $this
            ->get(
                HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_TYPE_MAP
            );
    }

    /**
     * @return array
     */
    public function getHeidelpayRestRecoverableErrors(): array
    {
        return $this
            ->get(
                HeidelpayRestConstants::HEIDELPAY_REST_RECOVERABLE_ERRORS
            );
    }
    /**
     * @return string
     */
    public function getHostName()
    {
        return $this->get(
            ApplicationConstants::HOST_ZED
        );
    }
}
