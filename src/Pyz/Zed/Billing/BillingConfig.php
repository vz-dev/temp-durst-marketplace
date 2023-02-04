<?php

namespace Pyz\Zed\Billing;

use DateTimeZone;
use Generated\Shared\Transfer\DurstCompanyTransfer;
use Generated\Shared\Transfer\SequenceNumberSettingsTransfer;
use Pyz\Shared\Billing\BillingConstants;
use Pyz\Shared\Mail\MailConstants;
use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;

class BillingConfig extends AbstractBundleConfig
{
    protected const DEFAULT_BILLING_PERIOD_GENERATE_DAYS_IN_ADVANCE = '1 day';
    protected const DEFAULT_TIME_ZONE = 'Europe/Berlin';

    protected const CSV_EXPORT_PAYMENT_TYPE_MAP = [
        'WholesaleOrderCreditCardItemStates' => 'Kreditkarte',
        'WholesaleOrderPayPalAuthorizationItemStates' => 'Paypal',
        'WholesaleOrderSepaDirectDebitItemStates' => 'SEPA ',
        'WholesaleOrderSepaDirectDebitGuaranteedItemStates' => 'SEPA (garantiert)',
        'WholesaleOrderInvoiceItemStates' => 'Rechnungskauf',
        'WholesaleOrderInvoiceGuaranteedItemStates' => 'Rechnungskauf (garantiert)',
    ];

    public const BILLING_PERIOD_REFERENCE_PREFIX = 'DBP';
    public const BILLING_PERIOD_REFERENCE_SEPARATOR = '-';

    /**
     * @param string $merchantIdentifier
     *
     * @return \Generated\Shared\Transfer\SequenceNumberSettingsTransfer
     */
    public function getBillingSequenceNumberSettingsTransfer(string $merchantIdentifier) : SequenceNumberSettingsTransfer
    {
        return (new SequenceNumberSettingsTransfer())
            ->setName($this->getInvoiceReferenceSequenceName($merchantIdentifier))
            ->setPrefix($this->getPrefix());
    }

    /**
     * @return string
     */
    public function getBillingPeriodDaysInAdvance() : string
    {
        return $this
            ->get(BillingConstants::BILLING_PERIOD_GENERATE_DAYS_IN_ADVANCE, self::DEFAULT_BILLING_PERIOD_GENERATE_DAYS_IN_ADVANCE);
    }

    /**
     * @return string
     */
    public function getBillingPeriodZipArchiveTempPath(): string
    {
        return $this
            ->get(
                BillingConstants::BILLING_PERIOD_ZIP_ARCHIVE_TEMP_PATH
            );
    }

    /**
     * @param int $idBranch
     * @param string $billingReference
     *
     * @return string
     */
    public function getZipArchiveFileName(
        int $idBranch,
        string $billingReference
    ): string {
        return sprintf(
            'abrechnung-%d-%s.zip',
            $idBranch,
            $billingReference
        );
    }

    /**
     * @return string
     */
    public function getBillingInvoiceMailTemplate(): string
    {
        return '@MerchantCenter/Mail/durst-merchant-billing-invoices.html.twig';
    }

    /**
     * @return \Generated\Shared\Transfer\DurstCompanyTransfer
     */
    public function getDurstCompanyTransfer() : DurstCompanyTransfer
    {
        return (new DurstCompanyTransfer())
            ->setName($this->get(MailConstants::MAIL_DURST_COMPANY_NAME))
            ->setStreet($this->get(MailConstants::MAIL_DURST_COMPANY_STREET))
            ->setCity($this->get(MailConstants::MAIL_DURST_COMPANY_CITY))
            ->setWeb($this->get(MailConstants::MAIL_DURST_COMPANY_WEB))
            ->setEmail($this->get(MailConstants::MAIL_DURST_COMPANY_EMAIL))
            ->setVatId($this->get(MailConstants::MAIL_DURST_COMPANY_VAT_ID))
            ->setBio($this->get(MailConstants::MAIL_DURST_COMPANY_BIO))
            ->setJurisdiction($this->get(MailConstants::MAIL_DURST_COMPANY_JURISDICTION))
            ->setManagement($this->get(MailConstants::MAIL_DURST_COMPANY_MANAGEMENT));
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this
            ->get(MailConstants::MAIL_ASSETS_BASE_URL);
    }

    /**
     * @return string
     */
    protected function getPrefix(): string
    {
        return sprintf(
            '%s%s',
            static::BILLING_PERIOD_REFERENCE_PREFIX,
            static::BILLING_PERIOD_REFERENCE_SEPARATOR
        );
    }

    /**
     * @param string $merchantIdentifier
     *
     * @return string
     */
    protected function getInvoiceReferenceSequenceName(string $merchantIdentifier): string
    {
        return sprintf(
            BillingConstants::BILLING_PERIOD_REFERENCE_SEQUENCE_NAME_FORMAT,
            $merchantIdentifier
        );
    }

    /**
     * @return \DateTimeZone
     */
    public function getProjectTimeZone(): DateTimeZone
    {
        $timeZoneString = $this
            ->get(ApplicationConstants::PROJECT_TIMEZONE, static::DEFAULT_TIME_ZONE);

        return new DateTimeZone($timeZoneString);
    }

    /**
     * @return array
     */
    public function getCsvExportPaymentTypeMap(): array
    {
        return static::CSV_EXPORT_PAYMENT_TYPE_MAP;
    }
}
