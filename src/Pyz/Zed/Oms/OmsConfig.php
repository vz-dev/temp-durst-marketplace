<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 22.01.18
 * Time: 12:01
 */

namespace Pyz\Zed\Oms;

use Generated\Shared\Transfer\SequenceNumberSettingsTransfer;
use Pyz\Shared\HeidelpayRest\HeidelpayRestConstants;
use Pyz\Shared\Mail\MailConstants;
use Pyz\Shared\Oms\OmsConstants;
use Pyz\Shared\Pdf\PdfConstants;
use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Zed\Oms\OmsConfig as SprykerOmsConfig;

class OmsConfig extends SprykerOmsConfig
{
    public const ORDER_PROCESS_RETAIL_ORDER = 'RetailOrder';
    public const DEFAULT_WHOLESALE_ORDER_PROCESS = 'WholesaleOrder';

    public const DEFAULT_MAIL_ASSETS_BASE_URL = 'https://media.durst.shop';
    public const DEFAULT_MAIL_MERCHANT_CENTER_BASE_URL = 'https://haendler.durst.shop';
    public const DEFAULT_MAIL_CUSTOMER_SURVEY_URL = 'https://www.surveymonkey.com/r/your_survey?ZIP=%s&MID=%d&DD=%s&OV=%s';
    public const DEFAULT_MAIL_CUSTOMER_SURVEY_HAPPINESS = ['plus', 'neutral', 'negative'];

    public const DEFAULT_PROJECT_TIME_ZONE = 'Europe/Berlin';

    public const DEFAULT_MAIL_CANCEL_ORDER_BASE_URL = 'http://127.0.0.1/cancel?t=%s';

    /**
     * @return string
     */
    public function getProcessDefinitionLocation()
    {
        return APPLICATION_ROOT_DIR . '/config/Zed/oms/';
    }

    /**
     * @return array
     */
    public function getActiveProcesses()
    {
        return $this
            ->get(OmsConstants::ACTIVE_PROCESSES, [self::ORDER_PROCESS_RETAIL_ORDER,]);
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this
            ->get(MailConstants::MAIL_ASSETS_BASE_URL, static::DEFAULT_MAIL_ASSETS_BASE_URL);
    }

    /**
     * @return string
     */
    public function getHaendlerBaseUrl()
    {
        return $this
            ->get(MailConstants::MAIL_MERCHANT_CENTER_BASE_URL, static::DEFAULT_MAIL_MERCHANT_CENTER_BASE_URL);
    }

    /**
     * @return string
     */
    public function getSurveyUrl() : string
    {
        return $this
            ->get(MailConstants::MAIL_CUSTOMER_SURVEY_URL, static::DEFAULT_MAIL_CUSTOMER_SURVEY_URL);
    }

    /**
     * @return array
     */
    public function getSurveyHappinessTypes() : array
    {
        return $this
            ->get(MailConstants::MAIL_CUSTOMER_SURVEY_HAPPINESS, static::DEFAULT_MAIL_CUSTOMER_SURVEY_HAPPINESS);
    }

    /**
     * @return string
     */
    public function getDurstCompanyName() : string
    {
        return $this
            ->get(MailConstants::MAIL_DURST_COMPANY_NAME);
    }

    /**
     * @return string
     */
    public function getDurstCompanyStreet() : string
    {
        return $this
            ->get(MailConstants::MAIL_DURST_COMPANY_STREET);
    }

    /**
     * @return string
     */
    public function getDurstCompanyCity() : string
    {
        return $this
            ->get(MailConstants::MAIL_DURST_COMPANY_CITY);
    }

    /**
     * @return string
     */
    public function getDurstCompanyWeb() : string
    {
        return $this
            ->get(MailConstants::MAIL_DURST_COMPANY_WEB);
    }

    /**
     * @return string
     */
    public function getDurstCompanyEmail() : string
    {
        return $this
            ->get(MailConstants::MAIL_DURST_COMPANY_EMAIL);
    }

    /**
     * @return string
     */
    public function getDurstCompanyVatId() : string
    {
        return $this
            ->get(MailConstants::MAIL_DURST_COMPANY_VAT_ID);
    }

    public function getDurstCompanyBio() : string
    {
        return $this
            ->get(MailConstants::MAIL_DURST_COMPANY_BIO);
    }

    /**
     * @return string
     */
    public function getDurstCompanyJurisdiction() : string
    {
        return $this
            ->get(MailConstants::MAIL_DURST_COMPANY_JURISDICTION);
    }

    /**
     * @return string
     */
    public function getDurstCompanyManagement() : string
    {
        return $this
            ->get(MailConstants::MAIL_DURST_COMPANY_MANAGEMENT);
    }

    /**
     * Separator for the sequence number
     *
     * @return string
     */
    public function getUniqueIdentifierSeparator() : string
    {
        return '-';
    }

    /**
     * Defines the prefix for the sequence number which is the public id of an order.
     *
     * @return SequenceNumberSettingsTransfer
     */
    public function getInvoiceReferenceDefaults() : SequenceNumberSettingsTransfer
    {
        $sequenceNumberSettingsTransfer = new SequenceNumberSettingsTransfer();

        $sequenceNumberSettingsTransfer->setName(OmsConstants::NAME_INVOICE_REFERENCE);

        $sequenceNumberPrefixParts = [];

        $sequenceNumberPrefixParts[] = $this->get(OmsConstants::INVOICE_PREFIX);
        $prefix = implode($this->getUniqueIdentifierSeparator(), $sequenceNumberPrefixParts) . $this->getUniqueIdentifierSeparator();
        $sequenceNumberSettingsTransfer->setPrefix($prefix);

        return $sequenceNumberSettingsTransfer;
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
     * @return array
     */
    public function getOldWholesaleProcess() : array
    {
        return $this
            ->get(OmsConstants::OLD_PROCESSES_WHOLESALE_ORDER);
    }

    /**
     * @return string
     */
    public function getPdfAssetPath() : string
    {
        return $this
            ->get(PdfConstants::PDF_ASSETS_PATH);
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
     * @return string
     */
    public function getWholeSaleAcceptedState(): string
    {
        return $this
            ->get(
                OmsConstants::OMS_WHOLESALE_ACCEPTED_STATE
            );
    }

    /**
     * @return string
     */
    public function getProjectTimeZone(): string
    {
        return $this
            ->get(
                ApplicationConstants::PROJECT_TIMEZONE,
                static::DEFAULT_PROJECT_TIME_ZONE
            );
    }

    /**
     * @return string
     */
    public function getCancelOrderBaseUrl(): string
    {
        return $this
            ->get(
                MailConstants::MAIL_CANCEL_ORDER_BASE_URL,
                static::DEFAULT_MAIL_CANCEL_ORDER_BASE_URL
            );
    }

    /**
     * @return array
     */
    public function getDeveloperMailRecipient(): array
    {
        return $this
            ->get(MailConstants::MAIL_RECIPIENT_DEVELOPER);
    }

    /**
     * @return array
     */
    public function getServiceMailRecipient(): array
    {
        return $this
            ->get(MailConstants::MAIL_RECIPIENT_SERVICE);
    }

    /**
     * @return string
     */
    public function getFridgeBaseUrl(): string
    {
        return $this->
            get(ApplicationConstants::BASE_URL_ZED);
    }

    /**
     * @return string
     */
    public function getRetailProcessName(): string
    {
        return $this
            ->get(OmsConstants::RETAIL_PROCESS_NAME);
    }
}
