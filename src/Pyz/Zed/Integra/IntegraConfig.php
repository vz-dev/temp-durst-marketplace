<?php

namespace Pyz\Zed\Integra;

use Pyz\Shared\Integra\IntegraConstants;
use Pyz\Shared\Pdf\PdfConstants;
use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;

class IntegraConfig extends AbstractBundleConfig
{
    /**
     * @return string
     */
    public function getLogLevel(): string
    {
        return $this
            ->get(IntegraConstants::INTEGRA_LOG_LEVEL);
    }

    /**
     * @return string
     */
    public function getTimezone(): string
    {
        return $this
            ->get(ApplicationConstants::PROJECT_TIMEZONE);
    }

    /**
     * @param string $receiptNo
     *
     * @return string
     */
    public function getPrefixedOrderReference(string $receiptNo): string
    {
        return sprintf(
            '%s%s',
            IntegraConstants::INTEGRA_REFERENCE_PREFIX,
            $receiptNo
        );
    }

    /**
     * @return string
     */
    public function getEncryptionCipherMethod(): string
    {
        return $this
            ->get(IntegraConstants::INTEGRA_ENCRYPTION_CIPHER_METHOD);
    }

    /**
     * @return string
     */
    public function getEncryptionKey(): string
    {
        return $this
            ->get(IntegraConstants::INTEGRA_ENCRYPTION_KEY);
    }

    /**
     * @return string
     */
    public function getEncryptionIv(): string
    {
        return $this
            ->get(IntegraConstants::INTEGRA_ENCRYPTION_IV);
    }

    /**
     * @return string
     */
    public function getCsvFileTmpPath(): string
    {
        return $this
            ->get(IntegraConstants::INTEGRA_CSV_FILE_TMP_PATH);
    }

    /**
     * @return string
     */
    public function getIntegraWebserviceSessionKey(): string
    {
        return IntegraConstants::INTEGRA_ORGASOFT_WEBSERVICE_SESSION_MANAGER_KEY;
    }

    /**
     * @return array
     */
    public function getBlacklistedProducts() : array
    {
        return IntegraConstants::INTEGRA_GBZ_PRODUCT_BLACKLIST;
    }

    /**
     * @return array
     */
    public function getProductMapMissingNrme() : array
    {
        return IntegraConstants::INTEGRA_GBZ_ITEM_MAP_NRME;
    }

    /**
     * @return string
     */
    public function getDeliveryNotePdfTemplate(): string
    {
        return '@Integra/Pdf/delivery-note-pdf.html.twig';
    }

    /**
     * @return string
     */
    public function getPdfAssetPath(): string
    {
        return $this
            ->get(PdfConstants::PDF_ASSETS_PATH);
    }

    /**
     * @return array
     */
    public function getGbzPaymentMap() : array
    {
        return IntegraConstants::INTEGRA_GBZ_PAYMENT_METHOD_MAP;
    }

    /**
     * @return string
     */
    public function getPdfDeliveryNotePath() : string
    {
        return $this
            ->get(IntegraConstants::PDF_DELIVERY_NOTE_SAVE_PATH);
    }
}
