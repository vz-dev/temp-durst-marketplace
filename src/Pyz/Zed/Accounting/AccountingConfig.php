<?php
/**
 * Durst - project - AccountingConfig.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 24.03.20
 * Time: 17:12
 */

namespace Pyz\Zed\Accounting;


use Generated\Shared\Transfer\SequenceNumberSettingsTransfer;
use Pyz\Shared\Accounting\AccountingConstants;
use Pyz\Shared\Oms\OmsConstants;
use Pyz\Shared\Tour\TourConstants;
use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;

class AccountingConfig extends AbstractBundleConfig
{
    protected const LICENSE_INVOICE_NAME_REFERENCE = 'LicenseInvoiceVariable';
    protected const LICENSE_INVOICE_NAME_REFERENCE_FIXED = 'LicenseInvoiceFixed';

    /**
     * @return string[]
     */
    public function getDeliveredState(): array
    {
        return $this
            ->get(AccountingConstants::OMS_WHOLESALE_PAYMENT_ACCOUNTING_STATES);
    }

    /**
     * @return \Generated\Shared\Transfer\SequenceNumberSettingsTransfer
     */
    public function getLicenseInvoiceReferenceDefaults(): SequenceNumberSettingsTransfer
    {
        $sequenceNumberSettingsTransfer = new SequenceNumberSettingsTransfer();

        $name = static::LICENSE_INVOICE_NAME_REFERENCE . $this->getUniqueIdentifierSeparator() . '%d';

        $sequenceNumberSettingsTransfer
            ->setName($name)
            ->setPrefix('');

        return $sequenceNumberSettingsTransfer;
    }

    /**
     * @return \Generated\Shared\Transfer\SequenceNumberSettingsTransfer
     */
    public function getLicenseInvoiceFixedReferenceDefaults(): SequenceNumberSettingsTransfer
    {
        $sequenceNumberSettingsTransfer = new SequenceNumberSettingsTransfer();

        $name = static::LICENSE_INVOICE_NAME_REFERENCE_FIXED . $this->getUniqueIdentifierSeparator() . '%d';

        $sequenceNumberSettingsTransfer
            ->setName($name)
            ->setPrefix('');

        return $sequenceNumberSettingsTransfer;
    }

    /**
     * @return string
     */
    public function getLicenseInvoiceFixedKey(): string
    {
        return $this
            ->get(
                AccountingConstants::INVOICE_LICENSE_FIXED
            );
    }

    /**
     * @return string
     */
    public function getLicenseInvoiceFixedReducedKey(): string
    {
        return $this
            ->get(
                AccountingConstants::INVOICE_LICENSE_FIXED_REDUCED
            );
    }

    /**
     * @return string
     */
    public function getLicenseInvoiceVariableKey(): string
    {
        return $this
            ->get(
                AccountingConstants::INVOICE_LICENSE_VARIABLE
            );
    }

    /**
     * @return string
     */
    public function getLicenseInvoiceVariableReducedKey(): string
    {
        return $this
            ->get(
                AccountingConstants::INVOICE_LICENSE_VARIABLE_REDUCED
            );
    }

    /**
     * @return string
     */
    public function getMarketingInvoiceFixedKey(): string
    {
        return $this
            ->get(
                AccountingConstants::INVOICE_MARKETING_FIXED
            );
    }

    /**
     * @return string
     */
    public function getMarketingInvoiceFixedReducedKey(): string
    {
        return $this
            ->get(
                AccountingConstants::INVOICE_MARKETING_FIXED_REDUCED
            );
    }

    /**
     * @return string
     */
    public function getMarketingInvoiceVariableKey(): string
    {
        return $this
            ->get(
                AccountingConstants::INVOICE_MARKETING_VARIABLE
            );
    }

    /**
     * @return string
     */
    public function getMarketingInvoiceVariableReducedKey(): string
    {
        return $this
            ->get(
                AccountingConstants::INVOICE_MARKETING_VARIABLE_REDUCED
            );
    }

    /**
     * @return string
     */
    public function getProjectTimeZone(): string
    {
        return $this
            ->get(
                ApplicationConstants::PROJECT_TIMEZONE
            );
    }

    /**
     * @return string
     */
    public function getRealaxDelimiter(): string
    {
        return $this
            ->get(
                AccountingConstants::REALAX_DELIMITER
            );
    }

    /**
     * @return string
     */
    public function getRealaxCsvLineFormat(): string
    {
        return $this
            ->get(
                AccountingConstants::REALAX_CSV_LINE_FORMAT
            );
    }

    /**
     * @return string
     */
    public function getRealaxExportPath(): string
    {
        return $this
            ->get(
                AccountingConstants::REALAX_EXPORT_PATH
            );
    }

    /**
     * @return array
     */
    public function getRealaxRecipients(): array
    {
        return $this
            ->get(
                AccountingConstants::REALAX_RECIPIENTS
            );
    }

    /**
     * @return array
     */
    public function getCoronaTaxReductionMonth(): array
    {
        return $this
            ->get(
                AccountingConstants::REALAX_CORONA_TAX_REDUCTION_MONTH
            );
    }

    /**
     * @return array
     */
    public function getCoronaTaxReductionYear(): array
    {
        return $this
            ->get(
                AccountingConstants::REALAX_CORONA_TAX_REDUCTION_YEAR
            );
    }

    /**
     * @return float
     */
    public function getRealaxTaxRateNormal(): float
    {
        return $this
            ->get(
                AccountingConstants::REALAX_NORMAL_TAX_RATE
            );
    }

    /**
     * @return float
     */
    public function getRealaxTaxRateCorona(): float
    {
        return $this
            ->get(
                AccountingConstants::REALAX_CORONA_TAX_RATE
            );
    }

    /**
     * @return int
     */
    public function getProcessTimeout(): int
    {
        return $this
            ->get(
                AccountingConstants::PROCESS_TIMEOUT
            );
    }

    /**
     * @return string
     */
    public function getPhpPathForConsole(): string
    {
        return $this
            ->get(
                TourConstants::PHP_PATH_FOR_CONSOLE
            );
    }

    /**
     * @return string
     */
    protected function getUniqueIdentifierSeparator(): string
    {
        return '-';
    }
}
