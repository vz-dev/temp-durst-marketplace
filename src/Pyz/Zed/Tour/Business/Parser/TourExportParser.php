<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2018-12-14
 * Time: 11:49
 */

namespace Pyz\Zed\Tour\Business\Parser;


use Pyz\Shared\Edifact\EdifactConstants;
use Pyz\Zed\Edifact\Business\EdifactFacadeInterface;
use Pyz\Zed\Tour\Business\Mapper\TourExportMapper;
use Pyz\Zed\Tour\TourConfig;

class TourExportParser
{
    protected const TAG_UNA = 'UNA';
    protected const TAG_UNB = 'UNB';
    protected const TAG_UNG = 'UNG';
    protected const TAG_UNH = 'UNH';
    protected const TAG_BGM = 'BGM';
    protected const TAG_DTM = 'DTM';
    protected const TAG_FTX = 'FTX';
    protected const TAG_RFF = 'RFF';
    protected const TAG_NAD = 'NAD';
    protected const TAG_CPS = 'CPS';
    protected const TAG_LIN = 'LIN';
    protected const TAG_PIA = 'PIA';
    protected const TAG_PRI = 'PRI';
    protected const TAG_IMD = 'IMD';
    protected const TAG_QTY = 'QTY';
    protected const TAG_UNS = 'UNS';
    protected const TAG_UNE = 'UNE';
    protected const TAG_UNT = 'UNT';
    protected const TAG_UNZ = 'UNZ';

    protected const SEPARATOR_GROUP = ':';
    protected const SEPARATOR_ELEMENT = '+';
    protected const SYMBOL_ESCAPE = '?';
    protected const SYMBOL_NEWLINE = '\'';

    protected const EXPORT_TYPE_ORDERS = 1;
    protected const EXPORT_TYPE_DEPOSIT = 2;

    protected const MESSAGE_TYPE_ORDERS = 'ORDERS';
    protected const MESSAGE_TYPE_DESADV = 'DESADV';

    protected const EDIFACT_D96A_UNA = self::TAG_UNA . ':+.? ' . self::SYMBOL_NEWLINE;
    protected const EDIFACT_D96A_UNB = self::TAG_UNB . '+UNOC:3+%s:14+%s:14+%s:%s+%s+%s+%s++++' . self::SYMBOL_NEWLINE;
    protected const EDIFACT_D96A_UNB_DEV = self::TAG_UNB . '+UNOC:3+%s:14+%s:14+%s:%s+%s+%s+%s++++1' . self::SYMBOL_NEWLINE;
    protected const EDIFACT_D96A_UNG = self::TAG_UNG . '+%s+%s:14+%s:14+%s:%s+1+UN+D:96A:EAN008' . self::SYMBOL_NEWLINE;
    protected const EDIFACT_D96A_UNH = self::TAG_UNH . '+%s+%s:D:96A:UN:EAN008' . self::SYMBOL_NEWLINE;
    protected const EDIFACT_D96A_BGM = self::TAG_BGM . '+%s::9+%s+9' . self::SYMBOL_NEWLINE;
    protected const EDIFACT_D96A_DTM_MESSAGE_DATE = self::TAG_DTM . '+137:%s:102' . self::SYMBOL_NEWLINE;
    protected const EDIFACT_D96A_DTM_MESSAGE_DATETIME = self::TAG_DTM . '+137:%s:203' . self::SYMBOL_NEWLINE;
    protected const EDIFACT_D96A_DTM_LOAD_DATE = self::TAG_DTM . '+200:%s:102' . self::SYMBOL_NEWLINE;
    protected const EDIFACT_D96A_DTM_LOAD_DATETIME = self::TAG_DTM . '+200:%s:203' . self::SYMBOL_NEWLINE;
    protected const EDIFACT_D96A_FTX_TRA = self::TAG_FTX . '+TRA+1++%s' . self::SYMBOL_NEWLINE;
    protected const EDIFACT_D96A_FTX_DEL = self::TAG_FTX . '+DEL+1++%s' . self::SYMBOL_NEWLINE;
    protected const EDIFACT_D96A_FTX_PMT = self::TAG_FTX . '+PMT+1++%s' . self::SYMBOL_NEWLINE;
    protected const EDIFACT_D96A_RFF_ON = self::TAG_RFF . '+ON:%s' . self::SYMBOL_NEWLINE;
    protected const EDIFACT_D96A_RFF_IT = self::TAG_RFF . '+IT:%s' . self::SYMBOL_NEWLINE;
    protected const EDIFACT_D96A_NAD_BUYER = self::TAG_NAD . '+BY+%s::9' . self::SYMBOL_NEWLINE;
    protected const EDIFACT_D96A_NAD_VENDOR = self::TAG_NAD . '+SU+%s::9' . self::SYMBOL_NEWLINE;
    protected const EDIFACT_D96A_NAD_DELIVERY_ADDRESS = self::TAG_NAD . '+DP+%s::9' . self::SYMBOL_NEWLINE;
    protected const EDIFACT_D96A_CPS = self::TAG_CPS . '+1' . self::SYMBOL_NEWLINE;
    protected const EDIFACT_D96A_LIN = self::TAG_LIN . '+%d++%s:EN' . self::SYMBOL_NEWLINE;
    protected const EDIFACT_D96A_PIA_BUYER = self::TAG_PIA . '+1+%s:BP::92' . self::SYMBOL_NEWLINE;
    protected const EDIFACT_D96A_PIA_VENDOR = self::TAG_PIA . '+1+%s:SA::91' . self::SYMBOL_NEWLINE;
    protected const EDIFACT_D96A_IMD = self::TAG_IMD . '+F++:::%s' . self::SYMBOL_NEWLINE;
    protected const EDIFACT_D96A_QTY = self::TAG_QTY . '+21:%d:PCE' . self::SYMBOL_NEWLINE;
    protected const EDIFACT_D96A_PRI_AAB = self::TAG_PRI . '+AAB:%d' . self::SYMBOL_NEWLINE;
    protected const EDIFACT_D96A_UNS = self::TAG_UNS . '+S' . self::SYMBOL_NEWLINE;
    protected const EDIFACT_D96A_UNT = self::TAG_UNT . '+%d+%s' . self::SYMBOL_NEWLINE;
    protected const EDIFACT_D96A_UNE = self::TAG_UNE . '+%d+1' . self::SYMBOL_NEWLINE;
    protected const EDIFACT_D96A_UNZ = self::TAG_UNZ . '+1+%s' . self::SYMBOL_NEWLINE;

    protected const ESCAPE_CHARS = [
        self::SYMBOL_ESCAPE,
        self::SEPARATOR_GROUP,
        self::SEPARATOR_ELEMENT,
        self::SYMBOL_NEWLINE,
    ];

    protected const UPLOAD_NAME_TEMPLATE = 'tour_export_%s_%s_%s';

    protected const DUMMY_ORDER_REFERENCE = 'DUMMY_ORDER';
    protected const DUMMY_ORDER_CUSTOMER_REFERENCE = 'DUMMY_CUSTOMER';
    protected const DUMMY_GTIN = '0';
    protected const DUMMY_MERCHANT_SKU = '0';
    protected const DUMMY_DURST_SKU = '0';
    protected const DUMMY_PRODUCT_DESCRIPTION = 'DUMMY_ITEM';
    protected const DUMMY_QUANTITY = 0;
    protected const DUMMY_ORDER_ITEM_PRICE_TO_PAY = 0;

    /**
     * @var TourConfig
     */
    protected $tourConfig;

    /**
     * @var EdifactFacadeInterface
     */
    protected $edifactFacade;

    /**
     * @var array
     */
    protected $exportData = [];

    /**
     * @var array
     */
    protected $edifact = [];

    /**
     * @var string
     */
    protected $uploadName = '';

    /**
     * @var string
     */
    protected $basicAuthUsername;

    /**
     * @var string
     */
    protected $basicAuthPassword;

    /**
     * @var string
     */
    protected $tourReference;

    /**
     * @var bool
     */
    protected $isDepositExport = false;

    /**
     * @var int
     */
    protected $exportType;

    /**
     * @var string
     */
    protected $exportVersion;

    /**
     * @var int
     */
    protected $messageCounter;

    /**
     * @var string
     */
    protected $messageType;

    /**
     * TourExportParser constructor.
     *
     * @param TourConfig $tourConfig
     * @param EdifactFacadeInterface $edifactFacade
     */
    public function __construct(TourConfig $tourConfig, EdifactFacadeInterface $edifactFacade)
    {
        $this->tourConfig = $tourConfig;
        $this->edifactFacade = $edifactFacade;
    }

    /**
     * @param array $exportRow
     * @return void
     */
    public function addExportRow(array $exportRow)
    {
        array_walk_recursive($exportRow, [$this, 'escapeValue']);

        $this->exportData[] = $exportRow;
    }

    /**
     * @return string
     */
    public function getParsedContent(): string
    {
        if (count($this->exportData) > 0) {
            $tourData = $this->exportData[0];

            $this->exportVersion = $this->edifactFacade->getExportVersion();

            $this->determineExportType($tourData);
            $this->determineMessageType();

            $this->createHeaderData($tourData);

            if ($this->exportVersion === EdifactConstants::EDIFACT_EXPORT_VERSION_1) {
                $productData = array_slice($this->exportData, 1);

                $this->createMessageData($tourData, $productData);
            } else if ($this->exportVersion === EdifactConstants::EDIFACT_EXPORT_VERSION_2) {
                $orderData = array_slice($this->exportData, 1);

                $this->messageCounter = 0;

                if (count($orderData) > 0) {
                    foreach ($orderData as $orderDataRecord) {
                        $this->messageCounter++;

                        $productData = $orderDataRecord[TourExportMapper::EDI_ORDER_ITEMS];

                        $this->createMessageData($tourData, $productData, $orderDataRecord);
                    }
                } else {
                    $this->messageCounter++;

                    $this->createMessageData($tourData, null, null, true);
                }
            }

            $this->createFooterData($tourData);
        }

        return join('', $this->edifact);
    }

    /**
     * @return string
     */
    public function getUploadName(): string
    {
        return $this
            ->uploadName;
    }

    /**
     * @return string|null
     */
    public function getBasicAuthUsername(): ?string
    {
        return $this
            ->basicAuthUsername;
    }

    /**
     * @return string|null
     */
    public function getBasicAuthPassword(): ?string
    {
        return $this
            ->basicAuthPassword;
    }

    /**
     * @return string
     */
    public function getTourReference(): string
    {
        return $this->tourReference;
    }

    /**
     * @return bool
     */
    public function isDepositExport(): bool
    {
        return $this->isDepositExport;
    }

    /**
     * @param array $headerData
     * @return void
     */
    protected function createHeaderData(array $headerData): void
    {
        $this->edifact[] = self::EDIFACT_D96A_UNA;

        $this->edifact[] = sprintf(
            $this->tourConfig->isEdifactTestrun() === false
                ? self::EDIFACT_D96A_UNB
                : self::EDIFACT_D96A_UNB_DEV,
            $headerData[TourExportMapper::EDI_ILN_RECIPIENT],
            $headerData[TourExportMapper::EDI_ILN_SENDER],
            $headerData[TourExportMapper::EDI_CREATE_DATE],
            $headerData[TourExportMapper::EDI_CREATE_TIME],
            $headerData[TourExportMapper::EDI_DATA_TRANSFER_REFERENCE],
            $headerData[TourExportMapper::EDI_ACCESS_TOKEN],
            $this->messageType
        );

        if ($this->exportVersion === EdifactConstants::EDIFACT_EXPORT_VERSION_2) {
            $this->edifact[] = sprintf(
                self::EDIFACT_D96A_UNG,
                $this->messageType,
                $headerData[TourExportMapper::EDI_ILN_RECIPIENT],
                $headerData[TourExportMapper::EDI_ILN_SENDER],
                $headerData[TourExportMapper::EDI_CREATE_DATE],
                $headerData[TourExportMapper::EDI_CREATE_TIME]
            );
        }
    }

    /**
     * @param array $tourData
     * @param array|null $productData
     * @param array|null $orderData
     * @param bool $dummy
     * @return void
     */
    protected function createMessageData(
        array $tourData,
        array $productData = null,
        array $orderData = null,
        bool $dummy = false
    ): void {
        $this->edifact[] = sprintf(
            self::EDIFACT_D96A_UNH,
            $this->getMessageReference($tourData),
            $this->messageType
        );

        $this->edifact[] = sprintf(
            self::EDIFACT_D96A_BGM,
            $this->getDocumentName(),
            $this->getDocumentNumber($tourData, $orderData, $dummy)
        );
        $this->edifact[] = sprintf(
            self::EDIFACT_D96A_DTM_MESSAGE_DATETIME,
            $tourData[TourExportMapper::EDI_CREATE_DATETIME]
        );
        $this->edifact[] = sprintf(
            self::EDIFACT_D96A_DTM_LOAD_DATETIME,
            $tourData[TourExportMapper::EDI_DELIVERY_DATETIME]
        );

        if ($this->exportVersion === EdifactConstants::EDIFACT_EXPORT_VERSION_1
            || ($this->exportVersion === EdifactConstants::EDIFACT_EXPORT_VERSION_2
                && $this->exportType === self::EXPORT_TYPE_ORDERS)
        ) {
            $this->createFreeTextData($tourData);
        }

        $this->edifact[] = sprintf(
            self::EDIFACT_D96A_RFF_ON,
            $this->getOrderNumberReference($tourData, $orderData, $dummy)
        );

        if ($this->exportVersion === EdifactConstants::EDIFACT_EXPORT_VERSION_2) {
            $this->edifact[] = sprintf(
                self::EDIFACT_D96A_RFF_IT,
                ($dummy !== true)
                    ? $orderData[TourExportMapper::EDI_ORDER_DURST_CUSTOMER_REFERENCE] ?? 'NONE'
                    : self::DUMMY_ORDER_CUSTOMER_REFERENCE
            );
        }

        $this->edifact[] = sprintf(
            self::EDIFACT_D96A_NAD_BUYER,
            $tourData[TourExportMapper::EDI_ILN_RECIPIENT]
        );
        $this->edifact[] = sprintf(
            self::EDIFACT_D96A_NAD_VENDOR,
            $tourData[TourExportMapper::EDI_ILN_SENDER]
        );
        $this->edifact[] = sprintf(
            self::EDIFACT_D96A_NAD_DELIVERY_ADDRESS,
            $tourData[TourExportMapper::EDI_ILN_DELIVERY]
        );

        if ($this->exportVersion === EdifactConstants::EDIFACT_EXPORT_VERSION_2
            && $this->exportType === self::EXPORT_TYPE_DEPOSIT
        ) {
            $this->edifact[] = sprintf(
                self::EDIFACT_D96A_CPS
            );

            $this->createFreeTextData($tourData);
        }

        $this->uploadName = sprintf(
            self::UPLOAD_NAME_TEMPLATE,
            $tourData[TourExportMapper::EDI_TOUR_NUMBER],
            $tourData[TourExportMapper::EDI_CREATE_DATE],
            $tourData[TourExportMapper::EDI_CREATE_TIME]
        );

        $this->basicAuthUsername = $tourData[TourExportMapper::EDI_BASIC_AUTH_USERNAME];
        $this->basicAuthPassword = $tourData[TourExportMapper::EDI_BASIC_AUTH_PASSWORD];

        $this->tourReference = $tourData[TourExportMapper::EDI_TOUR_NUMBER];

        $productCounter = 0;

        if ($productData !== null && count($productData) > 0) {
            foreach ($productData as $productDataRecord) {
                $productCounter++;

                $this->createProductData($productCounter, $productDataRecord);
            }
        } else if ($this->exportVersion === EdifactConstants::EDIFACT_EXPORT_VERSION_2) {
            $productCounter++;

            $this->createProductData($productCounter, null, true);
        }

        if ($this->exportVersion === EdifactConstants::EDIFACT_EXPORT_VERSION_1
            || ($this->exportVersion === EdifactConstants::EDIFACT_EXPORT_VERSION_2
                && $this->exportType === self::EXPORT_TYPE_ORDERS)
        ) {
            $this->edifact[] = self::EDIFACT_D96A_UNS;
        }

        $this->edifact[] = sprintf(
            self::EDIFACT_D96A_UNT,
            $this->countCurrentMessageSegments() + 1,
            $this->getMessageReference($tourData)
        );
    }

    /**
     * @param int $productCounter
     * @param array|null $productData
     * @param bool $dummy
     * @return void
     */
    protected function createProductData(int $productCounter, array $productData = null, bool $dummy = false)
    {
        $this->edifact[] = sprintf(
            self::EDIFACT_D96A_LIN,
            $productCounter,
            ($dummy !== true)
                ? $productData[TourExportMapper::EDI_GTIN]
                : self::DUMMY_GTIN
        );
        $this->edifact[] = sprintf(
            self::EDIFACT_D96A_PIA_VENDOR,
            ($dummy !== true)
                ? $productData[TourExportMapper::EDI_MERCHANT_SKU]
                : self::DUMMY_MERCHANT_SKU
        );

        if ($this->isDepositExport === false) {
            $this->edifact[] = sprintf(
                self::EDIFACT_D96A_PIA_BUYER,
                ($dummy !== true)
                    ? $productData[TourExportMapper::EDI_DURST_SKU]
                    : self::DUMMY_DURST_SKU
            );
        }

        $this->edifact[] = $this->createItemDescriptionSegment($productData, $dummy);

        $this->edifact[] = sprintf(
            self::EDIFACT_D96A_QTY,
            ($dummy !== true)
                ? $productData[TourExportMapper::EDI_QUANTITY]
                : self::DUMMY_QUANTITY
        );

        if ($this->exportVersion === EdifactConstants::EDIFACT_EXPORT_VERSION_2
            && $this->exportType === self::EXPORT_TYPE_ORDERS
        ) {
            $this->edifact[] = sprintf(
                self::EDIFACT_D96A_PRI_AAB,
                ($dummy !== true)
                    ? $productData[TourExportMapper::EDI_ORDER_ITEM_PRICE_TO_PAY]
                    : self::DUMMY_ORDER_ITEM_PRICE_TO_PAY
            );
        }
    }

    /**
     * @param array $footerData
     * @return void
     */
    protected function createFooterData(array $footerData)
    {
        if ($this->exportVersion === EdifactConstants::EDIFACT_EXPORT_VERSION_2) {
            $this->edifact[] = sprintf(
                self::EDIFACT_D96A_UNE,
                $this->messageCounter
            );
        }

        $this->edifact[] = sprintf(
            self::EDIFACT_D96A_UNZ,
            $footerData[TourExportMapper::EDI_DATA_TRANSFER_REFERENCE]
        );
    }

    /**
     * @return int
     */
    protected function countCurrentMessageSegments(): int
    {
        $count = 0;

        for ($i = count($this->edifact) - 1; $i >= 0; $i--) {
            $count++;

            if (substr($this->edifact[$i], 0, 3) === self::TAG_UNH) {
                break;
            }
        }

        return $count;
    }

    /**
     * @param mixed $value
     */
    protected function escapeValue(&$value): void
    {
        if (is_string($value) === false){
            return;
        }

        foreach (self::ESCAPE_CHARS as $escapeChar) {
            $value = str_replace($escapeChar, self::SYMBOL_ESCAPE . $escapeChar, $value);
        }
    }

    /**
     * @param array $tourData
     * @return string
     */
    protected function getMessageReference(array $tourData): string
    {
        if ($this->exportVersion === EdifactConstants::EDIFACT_EXPORT_VERSION_2) {
            return $tourData[TourExportMapper::EDI_DATA_TRANSFER_REFERENCE] . '-' . $this->messageCounter;
        }

        return $tourData[TourExportMapper::EDI_MESSAGE_REFERENCE];
    }

    protected function getDocumentName(): string
    {
        if ($this->exportVersion === EdifactConstants::EDIFACT_EXPORT_VERSION_2
            && $this->exportType === self::EXPORT_TYPE_DEPOSIT
        ) {
          return '230';
        }

        return '220';
    }

    /**
     * @param array $tourData
     * @param array|null $orderData
     * @param bool $dummy
     * @return string
     */
    protected function getDocumentNumber(array $tourData, array $orderData = null, bool $dummy = false): string
    {
        if ($this->exportVersion === EdifactConstants::EDIFACT_EXPORT_VERSION_2) {
            if ($dummy === true) {
                return self::DUMMY_ORDER_REFERENCE;
            }

            return $orderData[TourExportMapper::EDI_ORDER_REFERENCE];
        }

        return $this->exportType . '_' . $tourData[TourExportMapper::EDI_TOUR_NUMBER];
    }

    /**
     * @param array $tourData
     * @param array|null $orderData
     * @param bool $dummy
     * @return string
     */
    protected function getOrderNumberReference(array $tourData, array $orderData = null, bool $dummy = false): string
    {
        return $this->getDocumentNumber($tourData, $orderData, $dummy);
    }

    /**
     * @param array $tourData
     */
    protected function determineExportType(array $tourData): void
    {
        $this->exportType = self::EXPORT_TYPE_ORDERS;

        if ($tourData[TourExportMapper::EDI_IS_RETURN_ITEM] === true) {
            $this->exportType = self::EXPORT_TYPE_DEPOSIT;
            $this->isDepositExport = true;
        }
    }

    /**
     * @param array $tourData
     */
    protected function determineMessageType(): void
    {
        $this->messageType = self::MESSAGE_TYPE_ORDERS;

        if ($this->exportVersion === EdifactConstants::EDIFACT_EXPORT_VERSION_2 && $this->isDepositExport === true) {
            $this->messageType = self::MESSAGE_TYPE_DESADV;
        }
    }

    /**
     * @param array $tourData
     */
    protected function createFreeTextData(array $tourData): void
    {
        if ($this->exportVersion === EdifactConstants::EDIFACT_EXPORT_VERSION_2) {
            $this->edifact[] = sprintf(
                self::EDIFACT_D96A_FTX_TRA,
                $tourData[TourExportMapper::EDI_TOUR_NUMBER]
            );
        }

        $this->edifact[] = sprintf(
            self::EDIFACT_D96A_FTX_DEL,
            $tourData[TourExportMapper::EDI_DRIVER]
        );
        $this->edifact[] = sprintf(
            self::EDIFACT_D96A_FTX_PMT,
            $tourData[TourExportMapper::EDI_BILLING_REFERENCE]
        );
    }

    /**
     * @param array|null $productData
     * @param bool $dummy
     * @return string
     */
    protected function createItemDescriptionSegment(array $productData = null, bool $dummy = false): string
    {
        $segment = sprintf(
            self::EDIFACT_D96A_IMD,
            substr(
                ($dummy !== true)
                    ? $productData[TourExportMapper::EDI_PRODUCT_DESCRIPTION]
                    : self::DUMMY_PRODUCT_DESCRIPTION,
                0,
                35
            )
        );

        $secondToLastCharacter = substr($segment, -2, 1);

        if ($secondToLastCharacter === self::SYMBOL_ESCAPE) {
            $segment = substr($segment, 0, -2) . self::SYMBOL_NEWLINE;
        }

        return $segment;
    }
}
