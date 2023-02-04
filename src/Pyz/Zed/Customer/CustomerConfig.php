<?php
/**
 * Durst - project - CustomerConfig.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 22.11.21
 * Time: 10:54
 */

namespace Pyz\Zed\Customer;

use Pyz\Shared\Mail\MailConstants;
use Generated\Shared\Transfer\SequenceNumberSettingsTransfer;
use Pyz\Shared\Customer\CustomerConstants;
use Spryker\Zed\Customer\CustomerConfig as SprykerCustomerConfig;

class CustomerConfig extends SprykerCustomerConfig
{
    /**
     * @param int $idMerchant
     *
     * @return SequenceNumberSettingsTransfer
     */
    public function getDurstCustomerReferenceSequenceNumberSettings(int $idMerchant): SequenceNumberSettingsTransfer
    {
        $sequenceNumberSettings = new SequenceNumberSettingsTransfer();

        return $sequenceNumberSettings
            ->setName($this->getDurstCustomerReferenceSequenceName($idMerchant))
            ->setPrefix($this->getDurstCustomerReferencePrefix($idMerchant));
    }

    /**
     * @param int $idMerchant
     *
     * @return string
     */
    protected function getDurstCustomerReferenceSequenceName(int $idMerchant): string
    {
        return sprintf(
            CustomerConstants::DURST_CUSTOMER_REFERENCE_SEQUENCE_NAME_FORMAT,
            CustomerConstants::DURST_CUSTOMER_REFERENCE_SEQUENCE_NAME_MERCHANT_PREFIX,
            $idMerchant
        );
    }

    /**
     * @param int $idMerchant
     *
     * @return string
     */
    protected function getDurstCustomerReferencePrefix(int $idMerchant): string
    {
        return sprintf(
            CustomerConstants::DURST_CUSTOMER_REFERENCE_PREFIX_FORMAT,
            $idMerchant
        );
    }
    /**
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this
            ->get(
                MailConstants::MAIL_ASSETS_BASE_URL
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
