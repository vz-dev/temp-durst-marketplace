<?php
/**
 * Durst - project - IntegraConstants.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 08.11.20
 * Time: 11:17
 */

namespace Pyz\Shared\Integra;


use Pyz\Zed\Integra\Business\Model\Log\LoggerInterface;

interface IntegraConstants
{
    /**
     * As ftp credentials will be stored in db they need to be encrypted.
     * The following configuration defines how. The values should not be stored in a Repo.
     */
    public const INTEGRA_ENCRYPTION_CIPHER_METHOD = 'INTEGRA_ENCRYPTION_CIPHER_METHOD';
    public const INTEGRA_ENCRYPTION_KEY = 'INTEGRA_ENCRYPTION_KEY';
    public const INTEGRA_ENCRYPTION_IV = 'INTEGRA_ENCRYPTION_IV';

    /**
     * location where files will be stored temporarily until they get sent via ftp.
     */
    public const INTEGRA_CSV_FILE_TMP_PATH = 'DELIVERY_AREA_CSV_FILE_TMP_PATH';

    /**
     * minimum log level. Only entries with a higher level will be persisted
     */
    public const INTEGRA_LOG_LEVEL = 'INTEGRA_LOG_LEVEL';

    public const INTEGRA_LOG_LEVEL_INFO = LoggerInterface::LOG_LEVEL_INFO;
    public const INTEGRA_LOG_LEVEL_WARNING = LoggerInterface::LOG_LEVEL_WARNING;
    public const INTEGRA_LOG_LEVEL_ERROR = LoggerInterface::LOG_LEVEL_ERROR;

    /**
     * Soap Services provided by integra
     */
    public const INTEGRA_ORGASOFT_WEBSERVICE_WWS_KEY = 'IntegraWWS';
    public const INTEGRA_ORGASOFT_WEBSERVICE_SESSION_MANAGER_KEY = 'IntegraSessionManager';

    public const INTEGRA_ORGASOFT_WEBSERVICES = [
        self::INTEGRA_ORGASOFT_WEBSERVICE_WWS_KEY => '/wsdl/IntegraWWS',
        self::INTEGRA_ORGASOFT_WEBSERVICE_SESSION_MANAGER_KEY => '/wsdl/IntegraSessionManager',
    ];

    /**
     * The payment used for orders imported from integra, so no payment is necessary
     */
    public const INTEGRA_NO_PAYMENT = 'INTEGRA_NO_PAYMENT';

    /**
     * Order and tour references will be prefixed by this string when generated
     */
    public const INTEGRA_REFERENCE_PREFIX = 'INT-';

    public const INTEGRA_TOUR_NAME = 'INTEGRA-Tour';

    /**
     * Blacklisted Items for GBZ i.e. Kundenschlüssel, Staffelrabatte etc.
     */
    public const INTEGRA_GBZ_PRODUCT_BLACKLIST = ['9998', '8400'];

    /**
     * Special Items without NRME so custom mapping
     */
    public const INTEGRA_GBZ_ITEM_MAP_NRME = [
        '9995' => 'ST',
    ];

    public const DRIVER_APP_TOUR_FUTURE_CUTOFF_INTEGRA = '+2days midnight';

    public const INTEGRA_GBZ_PAYMENT_METHOD_MAP = [
        1 => 'per Rechnung',
        2 => 'Bankeinzug',
        3 => 'Lastschriftverfahren',
        5 => 'Barzahlung',
        6 => 'PayPal',
    ];

    public const INTEGRA_GBZ_PAYMENT_CODE_MAP = [
        1 => 'HeidelpayRestInvoice',
        2 => '',
        3 => 'HeidelpayRestSepaDirectDebit',
        5 => 'HeidelpayRestCashOnDelivery',
        6 => 'HeidelpayRestPayPalAuthorize',
    ];

    /**
     * Path where delivery note pdfs for external integra(GBZ) orders are saved
     */
    public const PDF_DELIVERY_NOTE_SAVE_PATH = 'PDF_DELIVERY_NOTE_SAVE_PATH';
}
