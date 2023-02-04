<?php

namespace Pyz\Zed\Invoice;

use Pyz\Shared\Invoice\InvoiceConfig as Config;
use Pyz\Shared\Invoice\InvoiceConstants;
use Pyz\Shared\Mail\MailConstants;
use Pyz\Shared\Pdf\PdfConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;

class InvoiceConfig extends AbstractBundleConfig
{
    protected const DEFAULT_PREFIX = 'D';
    protected const DEFAULT_SEPARATOR = '-';

    public const DEFAULT_MAIL_ASSETS_BASE_URL = 'https://haendler.durst.shop';
    public const DEFAULT_MAIL_CUSTOMER_SURVEY_URL = 'https://www.surveymonkey.com/r/your_survey?ZIP=%s&MID=%d&DD=%s&OV=%s';
    public const DEFAULT_MAIL_CUSTOMER_SURVEY_HAPPINESS = ['plus', 'neutral', 'negative'];

    /**
     * @return string
     */
    public function getReferencePrefix(): string
    {
        return $this->get(Config::INVOICE_REFERENCE_PREFIX, static::DEFAULT_PREFIX);
    }

    /**
     * @return string
     */
    public function getReferenceSeperator(): string
    {
        return $this->get(Config::INVOICE_REFERENCE_SEPARATOR, static::DEFAULT_SEPARATOR);
    }

    /**
     * @param string $merchantIdentifier
     *
     * @return string
     */
    public function getInvoiceReferenceSequenceName(string $merchantIdentifier): string
    {
        return sprintf(
            InvoiceConstants::INVOICE_REFERENCE_SEQUENCE_NAME_FORMAT,
            $merchantIdentifier
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
     * @return array
     */
    public function getSurveyHappinessTypes(): array
    {
        return $this
            ->get(
                MailConstants::MAIL_CUSTOMER_SURVEY_HAPPINESS,
                static::DEFAULT_MAIL_CUSTOMER_SURVEY_HAPPINESS
            );
    }

    /**
     * @return string
     */
    public function getSurveyUrl(): string
    {
        return $this
            ->get(
                MailConstants::MAIL_CUSTOMER_SURVEY_URL,
                static::DEFAULT_MAIL_CUSTOMER_SURVEY_URL
            );
    }

    /**
     * @return string
     */
    public function getPdfMailToPdfTemplate(): string
    {
        return $this
            ->get(PdfConstants::PDF_MAIL_TO_PDF_TEMPLATE);
    }
}
