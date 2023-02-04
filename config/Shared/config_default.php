<?php

use Monolog\Logger;
use Pyz\Shared\Accounting\AccountingConstants;
use Pyz\Shared\AkeneoPimMiddlewareConnector\AkeneoPimMiddlewareConnectorConstants;
use Pyz\Shared\AppRestApi\AppRestApiConstants;
use Pyz\Shared\Auth\AuthConstants;
use Pyz\Shared\Billing\BillingConstants;
use Pyz\Shared\Campaign\CampaignConstants;
use Pyz\Shared\CancelOrder\CancelOrderConstants;
use Pyz\Shared\Collector\CollectorConstants;
use Pyz\Shared\DeliveryArea\DeliveryAreaConstants;
use Pyz\Shared\DriverApp\DriverAppConfig;
use Pyz\Shared\Easybill\EasybillConfig;
use Pyz\Shared\Easybill\EasybillConstants;
use Pyz\Shared\GoogleApi\GoogleApiConstants;
use Pyz\Shared\Graphhopper\GraphhopperConstants;
use Pyz\Shared\GraphMasters\GraphMastersConstants;
use Pyz\Shared\HeidelpayRest\HeidelpayRestConstants;
use Pyz\Shared\Integra\IntegraConstants;
use Pyz\Shared\Invoice\InvoiceConfig;
use Pyz\Shared\Log\LogConstants;
use Pyz\Shared\Mail\MailConstants;
use Pyz\Shared\MerchantPrice\MerchantPriceConstants;
use Pyz\Shared\Oms\OmsConstants;
use Pyz\Shared\Pdf\PdfConstants;
use Pyz\Shared\PriceImport\PriceImportConstants;
use Pyz\Shared\Product\ProductConstants;
use Pyz\Shared\ProductExport\ProductExportConstants;
use Pyz\Shared\RetailPayment\RetailPaymentConfig;
use Pyz\Shared\Sales\SalesConstants;
use Pyz\Shared\Search\SearchConstants;
use Pyz\Shared\Sentry\SentryConstants;
use Pyz\Shared\Setup\SetupConstants;
use Pyz\Shared\SoftwarePackage\SoftwarePackageConstants;
use Pyz\Shared\Tax\TaxConstants;
use Pyz\Shared\TermsOfService\TermsOfServiceConstants;
use Pyz\Shared\Tour\TourConstants;
use Pyz\Zed\HeidelpayRest\Business\Exception\ConnectorAcquirerCurrentlyDownException;
use Pyz\Zed\HeidelpayRest\Business\Exception\CoreTimeoutAuthorizeException;
use Pyz\Zed\HeidelpayRest\Business\Exception\CoreTimeoutCancellationException;
use Pyz\Zed\HeidelpayRest\Business\Exception\CoreTimeoutChargeException;
use Pyz\Zed\Tour\Communication\Plugin\StateMachine\TourStateMachineHandlerPlugin;
use Spryker\Client\RabbitMq\Model\RabbitMqAdapter;
use Spryker\Shared\Acl\AclConstants;
use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Shared\Cms\CmsConstants;
use Spryker\Shared\CmsGui\CmsGuiConstants;
use Spryker\Shared\Customer\CustomerConstants;
use Spryker\Shared\ErrorHandler\ErrorHandlerConstants;
use Spryker\Shared\ErrorHandler\ErrorRenderer\WebHtmlErrorRenderer;
use Spryker\Shared\Event\EventConstants;
use Spryker\Shared\EventBehavior\EventBehaviorConstants;
use Spryker\Shared\EventJournal\EventJournalConstants;
use Spryker\Shared\FileSystem\FileSystemConstants;
use Spryker\Shared\Flysystem\FlysystemConstants;
use Spryker\Shared\GlueApplication\GlueApplicationConstants;
use Spryker\Shared\Kernel\ClassResolver\Cache\Provider\File;
use Spryker\Shared\Kernel\KernelConstants;
use Spryker\Shared\Kernel\Store;
use Spryker\Shared\NewRelic\NewRelicConstants;
use Spryker\Shared\Oauth\OauthConstants;
use Spryker\Shared\Propel\PropelConstants;
use Spryker\Shared\Queue\QueueConfig;
use Spryker\Shared\Queue\QueueConstants;
use Spryker\Shared\SequenceNumber\SequenceNumberConstants;
use Spryker\Shared\Session\SessionConstants;
use Spryker\Shared\Storage\StorageConstants;
use Spryker\Shared\Twig\TwigConstants;
use Spryker\Shared\User\UserConstants;
use Spryker\Shared\ZedNavigation\ZedNavigationConstants;
use Spryker\Shared\ZedRequest\ZedRequestConstants;
use Spryker\Yves\Log\Plugin\YvesLoggerConfigPlugin;
use Spryker\Zed\Log\Communication\Plugin\ZedLoggerConfigPlugin;
use Spryker\Zed\Oms\OmsConfig;
use Spryker\Zed\Propel\PropelConfig;
use SprykerEco\Shared\AkeneoPim\AkeneoPimConstants;
use SprykerEco\Shared\Loggly\LogglyConstants;

$CURRENT_STORE = Store::getInstance()->getStoreName();

// ---------- General environment
$config[KernelConstants::SPRYKER_ROOT] = APPLICATION_ROOT_DIR . '/vendor/spryker';
$config[ApplicationConstants::PROJECT_TIMEZONE] = 'Europe/Berlin';
$config[ApplicationConstants::ENABLE_WEB_PROFILER] = false;

$ENVIRONMENT_PREFIX = '';
$config[SequenceNumberConstants::ENVIRONMENT_PREFIX] = $ENVIRONMENT_PREFIX;
$config[SalesConstants::ENVIRONMENT_PREFIX] = $ENVIRONMENT_PREFIX;

$config[OmsConstants::ENVIRONMENT_PREFIX] = $ENVIRONMENT_PREFIX;
$config[OmsConstants::INVOICE_PREFIX] = 'D';

// ---------- Namespaces
$config[KernelConstants::PROJECT_NAMESPACE] = 'Pyz';
$config[KernelConstants::PROJECT_NAMESPACES] = [
    'Pyz',
];
$config[KernelConstants::CORE_NAMESPACES] = [
    'SprykerEco',
    'SprykerShop',
    'SprykerMiddleware',
    'Spryker',
    'Glue',
    'GlueApplicationExtension',
];

// ---------- Wholesale Tour
$config[TourConstants::TOUR_STATE_MACHINE_PROCESS] = TourStateMachineHandlerPlugin::PROCESS_WHOLESALE_TOUR;

// ---------- Propel
$config[PropelConstants::ZED_DB_ENGINE_MYSQL] = PropelConfig::DB_ENGINE_MYSQL;
$config[PropelConstants::ZED_DB_ENGINE_PGSQL] = PropelConfig::DB_ENGINE_PGSQL;
$config[PropelConstants::ZED_DB_SUPPORTED_ENGINES] = [
    PropelConfig::DB_ENGINE_MYSQL => 'MySql',
    PropelConfig::DB_ENGINE_PGSQL => 'PostgreSql',
];
$config[PropelConstants::SCHEMA_FILE_PATH_PATTERN] = APPLICATION_VENDOR_DIR . '/*/*/src/*/Zed/*/Persistence/Propel/Schema/';
$config[PropelConstants::USE_SUDO_TO_MANAGE_DATABASE] = true;
$config[PropelConstants::PROPEL_DEBUG] = false;

// ---------- Authentication
$config[UserConstants::USER_SYSTEM_USERS] = [
    'yves_system',
];

$AUTH_ZED_ENABLED = true;
$config[AuthConstants::AUTH_ZED_ENABLED] = $AUTH_ZED_ENABLED;
$config[ZedRequestConstants::AUTH_ZED_ENABLED] = $AUTH_ZED_ENABLED;
$config[AuthConstants::AUTH_DEFAULT_CREDENTIALS] = [
    'yves_system' => [
        'rules' => [
            [
                'bundle' => '*',
                'controller' => 'gateway',
                'action' => '*',
            ],
        ],
        // Please replace this token for your project
        'token' => 'EgWrZLBDmBkbNzesbmDte65r3LKWQ65Hbvv8WBBD4fkGQY2h4SCjzWpCry5kBzTVcJyXnNgWSzzBuszV',
    ],
];

// ---------- ACL
// ACL: Allow or disallow of urls for Zed Admin GUI for ALL users
$config[AclConstants::ACL_DEFAULT_RULES] = [
    [
        'bundle' => 'auth',
        'controller' => 'login',
        'action' => 'index',
        'type' => 'allow',
    ],
    [
        'bundle' => 'auth',
        'controller' => 'login',
        'action' => 'check',
        'type' => 'allow',
    ],
    [
        'bundle' => 'auth',
        'controller' => 'password',
        'action' => 'reset',
        'type' => 'allow',
    ],
    [
        'bundle' => 'auth',
        'controller' => 'password',
        'action' => 'reset-request',
        'type' => 'allow',
    ],
    [
        'bundle' => 'acl',
        'controller' => 'index',
        'action' => 'denied',
        'type' => 'allow',
    ],
    [
        'bundle' => 'heartbeat',
        'controller' => 'index',
        'action' => 'index',
        'type' => 'allow',
    ],
    [
        'bundle' => 'cancel-order',
        'controller' => 'cancel',
        'action' => 'index',
        'type' => 'allow',
    ],
];
// ACL: Allow or disallow of urls for Zed Admin GUI
$config[AclConstants::ACL_USER_RULE_WHITELIST] = [
    [
        'bundle' => 'application',
        'controller' => '*',
        'action' => '*',
        'type' => 'allow',
    ],
    [
        'bundle' => 'auth',
        'controller' => '*',
        'action' => '*',
        'type' => 'allow',
    ],
    [
        'bundle' => 'heartbeat',
        'controller' => 'heartbeat',
        'action' => 'index',
        'type' => 'allow',
    ],
    [
        'bundle' => 'cancel-order',
        'controller' => 'cancel',
        'action' => 'index',
        'type' => 'allow',
    ],
];
// ACL: Special rules for specific users
$config[AclConstants::ACL_DEFAULT_CREDENTIALS] = [
    'yves_system' => [
        'rules' => [
            [
                'bundle' => '*',
                'controller' => 'gateway',
                'action' => '*',
                'type' => 'allow',
            ],
        ],
    ],
];

// ---------- Elasticsearch
$ELASTICA_HOST = 'localhost';
$config[SearchConstants::ELASTICA_PARAMETER__HOST] = $ELASTICA_HOST;
$ELASTICA_TRANSPORT_PROTOCOL = 'http';
$config[SearchConstants::ELASTICA_PARAMETER__TRANSPORT] = $ELASTICA_TRANSPORT_PROTOCOL;
$ELASTICA_PORT = '10005';
$config[SearchConstants::ELASTICA_PARAMETER__PORT] = $ELASTICA_PORT;
$ELASTICA_AUTH_HEADER = '';
$config[SearchConstants::ELASTICA_PARAMETER__AUTH_HEADER] = $ELASTICA_AUTH_HEADER;
$ELASTICA_INDEX_NAME = null;// Store related config
$config[SearchConstants::ELASTICA_PARAMETER__INDEX_NAME] = $ELASTICA_INDEX_NAME;
$config[CollectorConstants::ELASTICA_PARAMETER__INDEX_NAME] = $ELASTICA_INDEX_NAME;
$ELASTICE_TIME_SLOT_INDEX_NAME = null;
$config[SearchConstants::ELASTICA_PARAMETER__TIME_SLOT_INDEX_NAME] = $ELASTICE_TIME_SLOT_INDEX_NAME;
$config[CollectorConstants::ELASTICA_PARAMETER__TIME_SLOT_INDEX_NAME] = $ELASTICE_TIME_SLOT_INDEX_NAME;
$ELASTICA_DOCUMENT_TYPE = 'page';
$config[SearchConstants::ELASTICA_PARAMETER__DOCUMENT_TYPE] = $ELASTICA_DOCUMENT_TYPE;
$config[CollectorConstants::ELASTICA_PARAMETER__DOCUMENT_TYPE] = $ELASTICA_DOCUMENT_TYPE;
$ELASTICA_TIME_SLOT_DOCUMENT_TYPE = 'time_slot';
$config[SearchConstants::ELASTICA_PARAMETER__TIME_SLOT_DOCUMENT_TYPE] = $ELASTICA_TIME_SLOT_DOCUMENT_TYPE;
$config[CollectorConstants::ELASTICA_PARAMETER__TIME_SLOT_DOCUMENT_TYPE] = $ELASTICA_TIME_SLOT_DOCUMENT_TYPE;
$ELASTICA_PARAMETER__EXTRA = [];
$config[SearchConstants::ELASTICA_PARAMETER__EXTRA] = $ELASTICA_PARAMETER__EXTRA;

// ----------- Elasticsearch - Graphmaster Timeslot Index Settings
$ELASTICA_GRAPHMASTERS_TIME_SLOT_INDEX_NAME = null;
$config[SearchConstants::ELASTICA_PARAMETER__GM_TIME_SLOT_INDEX_NAME] = $ELASTICA_GRAPHMASTERS_TIME_SLOT_INDEX_NAME;
$config[CollectorConstants::ELASTICA_PARAMETER__GM_TIME_SLOT_INDEX_NAME] = $ELASTICA_GRAPHMASTERS_TIME_SLOT_INDEX_NAME;
$ELASTICA_GRAPHMASTERS_TIME_SLOT_DOCUMENT_TYPE = 'gm_time_slot';
$config[SearchConstants::ELASTICA_PARAMETER__GM_TIME_SLOT_DOCUMENT_TYPE] = $ELASTICA_GRAPHMASTERS_TIME_SLOT_DOCUMENT_TYPE;
$config[CollectorConstants::ELASTICA_PARAMETER__GM_TIME_SLOT_DOCUMENT_TYPE] = $ELASTICA_GRAPHMASTERS_TIME_SLOT_DOCUMENT_TYPE;

// ---------- Page search
$config[SearchConstants::FULL_TEXT_BOOSTED_BOOSTING_VALUE] = 3;
$config[SearchConstants::SEARCH_INDEX_NAME_SUFFIX] = '';

// ---------- Twig
$config[TwigConstants::YVES_TWIG_OPTIONS] = [
    'cache' => new Twig_Cache_Filesystem(
        sprintf(
            '%s/data/%s/cache/Yves/twig',
            APPLICATION_ROOT_DIR,
            $CURRENT_STORE
        ),
        Twig_Cache_Filesystem::FORCE_BYTECODE_INVALIDATION
    ),
];
$config[TwigConstants::ZED_TWIG_OPTIONS] = [
    'cache' => new Twig_Cache_Filesystem(
        sprintf(
            '%s/data/%s/cache/Zed/twig',
            APPLICATION_ROOT_DIR,
            $CURRENT_STORE
        ),
        Twig_Cache_Filesystem::FORCE_BYTECODE_INVALIDATION
    ),
];
$config[TwigConstants::YVES_PATH_CACHE_FILE] = sprintf(
    '%s/data/%s/cache/Yves/twig/.pathCache',
    APPLICATION_ROOT_DIR,
    $CURRENT_STORE
);
$config[TwigConstants::ZED_PATH_CACHE_FILE] = sprintf(
    '%s/data/%s/cache/Zed/twig/.pathCache',
    APPLICATION_ROOT_DIR,
    $CURRENT_STORE
);

// ---------- Navigation
// The cache should always be activated. Refresh/build with CLI command: vendor/bin/console application:build-navigation-cache
$config[ZedNavigationConstants::ZED_NAVIGATION_CACHE_ENABLED] = true;

// ---------- Zed request
$config[ZedRequestConstants::TRANSFER_USERNAME] = 'yves';
$config[ZedRequestConstants::TRANSFER_PASSWORD] = 'o7&bg=Fz;nSslHBC';
$config[ZedRequestConstants::TRANSFER_DEBUG_SESSION_FORWARD_ENABLED] = false;
$config[ZedRequestConstants::TRANSFER_DEBUG_SESSION_NAME] = 'XDEBUG_SESSION';

$config[ZedRequestConstants::CLIENT_OPTIONS] = [
    'timeout' => 300,
    'connect_timeout' => 1.5,
];

// ---------- KV storage
$config[StorageConstants::STORAGE_KV_SOURCE] = 'redis';
$config[StorageConstants::STORAGE_PERSISTENT_CONNECTION] = true;

// ---------- Session
$config[SessionConstants::YVES_SESSION_SAVE_HANDLER] = SessionConstants::SESSION_HANDLER_REDIS_LOCKING;
$config[SessionConstants::YVES_SESSION_TIME_TO_LIVE] = SessionConstants::SESSION_LIFETIME_1_HOUR;
$config[SessionConstants::YVES_SESSION_COOKIE_TIME_TO_LIVE] = SessionConstants::SESSION_LIFETIME_0_5_HOUR;
$config[SessionConstants::YVES_SESSION_FILE_PATH] = session_save_path();
$config[SessionConstants::YVES_SESSION_PERSISTENT_CONNECTION] = $config[StorageConstants::STORAGE_PERSISTENT_CONNECTION];
$config[SessionConstants::ZED_SESSION_SAVE_HANDLER] = SessionConstants::SESSION_HANDLER_REDIS;
$config[SessionConstants::ZED_SESSION_TIME_TO_LIVE] = SessionConstants::SESSION_LIFETIME_1_HOUR;
$config[SessionConstants::ZED_SESSION_COOKIE_TIME_TO_LIVE] = SessionConstants::SESSION_LIFETIME_BROWSER_SESSION;
$config[SessionConstants::ZED_SESSION_FILE_PATH] = session_save_path();
$config[SessionConstants::ZED_SESSION_PERSISTENT_CONNECTION] = $config[StorageConstants::STORAGE_PERSISTENT_CONNECTION];
$config[SessionConstants::SESSION_HANDLER_REDIS_LOCKING_TIMEOUT_MILLISECONDS] = 0;
$config[SessionConstants::SESSION_HANDLER_REDIS_LOCKING_RETRY_DELAY_MICROSECONDS] = 0;
$config[SessionConstants::SESSION_HANDLER_REDIS_LOCKING_LOCK_TTL_MILLISECONDS] = 0;

// ---------- Cookie
$config[ApplicationConstants::YVES_COOKIE_DEVICE_ID_NAME] = 'did';
$config[ApplicationConstants::YVES_COOKIE_DEVICE_ID_VALID_FOR] = '+5 year';
$config[ApplicationConstants::YVES_COOKIE_VISITOR_ID_NAME] = 'vid';
$config[ApplicationConstants::YVES_COOKIE_VISITOR_ID_VALID_FOR] = '+30 minute';

// ---------- HTTP strict transport security
$HSTS_ENABLED = false;
$config[ApplicationConstants::ZED_HTTP_STRICT_TRANSPORT_SECURITY_ENABLED] = $HSTS_ENABLED;
$config[ApplicationConstants::YVES_HTTP_STRICT_TRANSPORT_SECURITY_ENABLED] = $HSTS_ENABLED;
$HSTS_CONFIG = [
    'max_age' => 31536000,
    'include_sub_domains' => true,
    'preload' => true,
];
$config[ApplicationConstants::ZED_HTTP_STRICT_TRANSPORT_SECURITY_CONFIG] = $HSTS_CONFIG;
$config[ApplicationConstants::YVES_HTTP_STRICT_TRANSPORT_SECURITY_CONFIG] = $HSTS_CONFIG;

// ---------- SSL
$config[SessionConstants::YVES_SSL_ENABLED] = false;
$config[ApplicationConstants::YVES_SSL_ENABLED] = false;
$config[ApplicationConstants::YVES_SSL_EXCLUDED] = [
    'heartbeat' => '/heartbeat',
    'cancel-order' => '/cancel-order',
];

$config[ZedRequestConstants::ZED_API_SSL_ENABLED] = false;
$config[ApplicationConstants::ZED_SSL_ENABLED] = false;
$config[ApplicationConstants::ZED_SSL_EXCLUDED] = [
    'heartbeat/index',
];

// ---------- Theme
$YVES_THEME = 'default';
$config[TwigConstants::YVES_THEME] = $YVES_THEME;
$config[CmsConstants::YVES_THEME] = $YVES_THEME;

// ---------- Error handling
$config[ErrorHandlerConstants::YVES_ERROR_PAGE] = APPLICATION_ROOT_DIR . '/public/Yves/errorpage/error.html';
$config[ErrorHandlerConstants::ZED_ERROR_PAGE] = APPLICATION_ROOT_DIR . '/public/Zed/errorpage/error.html';
$config[ErrorHandlerConstants::ERROR_RENDERER] = WebHtmlErrorRenderer::class;
// Due to some deprecation notices we silence all deprecations for the time being
$config[ErrorHandlerConstants::ERROR_LEVEL] = E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED;
// To only log e.g. deprecations instead of throwing exceptions here use
//$config[ErrorHandlerConstants::ERROR_LEVEL] = E_ALL
//$config[ErrorHandlerConstants::ERROR_LEVEL_LOG_ONLY] = E_DEPRECATED | E_USER_DEPRECATED;

// ---------- Logging
$config[LogConstants::LOGGER_CONFIG_ZED] = ZedLoggerConfigPlugin::class;
$config[LogConstants::LOGGER_CONFIG_YVES] = YvesLoggerConfigPlugin::class;

$config[LogConstants::LOG_LEVEL] = Logger::INFO;

$baseLogFilePath = sprintf('%s/data/%s/logs', APPLICATION_ROOT_DIR, $CURRENT_STORE);

$config[LogConstants::LOG_FILE_PATH_YVES] = $baseLogFilePath . '/YVES/application.log';
$config[LogConstants::LOG_FILE_PATH_ZED] = $baseLogFilePath . '/ZED/application.log';

$config[LogConstants::EXCEPTION_LOG_FILE_PATH_YVES] = $baseLogFilePath . '/YVES/exception.log';
$config[LogConstants::EXCEPTION_LOG_FILE_PATH_ZED] = $baseLogFilePath . '/ZED/exception.log';

$config[LogConstants::LOG_SANITIZE_FIELDS] = [
    'password',
];

$config[LogConstants::LOG_QUEUE_NAME] = 'log-queue';
$config[LogConstants::LOG_ERROR_QUEUE_NAME] = 'error-log-queue';

$config[LogConstants::LOG_MAIL_RECIPIENTS] = [
    'oliver.gail@durst.shop' => 'Oliver Gail',
];

$config[LogConstants::LOG_SENTRY_HANDLER_ENABLED_FOR_ENVIRONMENTS] = ['staging', 'demo', 'production'];

// ---------- Sentry
$config[SentryConstants::SENTRY_DSN] = 'https://0ae1890f45224900af133f1108c79db0@o325023.ingest.sentry.io/5609959';

/**
 * As long EventJournal is in ZedRequest bundle this needs to be disabled by hand
 */
$config[EventJournalConstants::DISABLE_EVENT_JOURNAL] = true;

// ---------- Auto-loader
$config[KernelConstants::AUTO_LOADER_CACHE_FILE_NO_LOCK] = false;
$config[KernelConstants::AUTO_LOADER_UNRESOLVABLE_CACHE_ENABLED] = false;
$config[KernelConstants::AUTO_LOADER_UNRESOLVABLE_CACHE_PROVIDER] = File::class;

// ---------- Dependency injector
$config[KernelConstants::DEPENDENCY_INJECTOR_YVES] = [
    'Checkout' => [
        'DummyPayment',
        RetailPaymentConfig::PROVIDER_NAME,
    ],
];
$config[KernelConstants::DEPENDENCY_INJECTOR_ZED] = [
    'Payment' => [
        RetailPaymentConfig::PROVIDER_NAME,
    ],
    'Oms' => [
        RetailPaymentConfig::PROVIDER_NAME,
    ],
];

// ---------- State machine (OMS)
$config[OmsConstants::PROCESS_LOCATION] = [
    OmsConfig::DEFAULT_PROCESS_LOCATION,
];
$config[OmsConstants::ACTIVE_PROCESSES] = [
    'RetailOrder',
    'WholesaleOrderPayPalAuthorization',
    'WholesaleOrderSepaDirectDebit',
    'WholesaleOrderCreditCard',
    'WholesaleOrderPayPalAuthorizationItemStates',
    'WholesaleOrderSepaDirectDebitItemStates',
    'WholesaleOrderSepaDirectDebitGuaranteedItemStates',
    'WholesaleOrderCreditCardItemStates',
    'WholesaleOrder',
    'WholesaleOrderInvoiceItemStates',
    'WholesaleOrderInvoiceGuaranteedItemStates',
    'Integra',
    'WholesaleOrderPaymentOnDeliveryItemStates',
];
$config[SalesConstants::PAYMENT_METHOD_STATEMACHINE_MAPPING] = [
    RetailPaymentConfig::PAYMENT_METHOD_EC => 'RetailOrder',
    RetailPaymentConfig::PAYMENT_METHOD_CASH => 'RetailOrder',
    RetailPaymentConfig::PAYMENT_METHOD_INVOICE => 'RetailOrder',
    RetailPaymentConfig::PAYMENT_METHOD_INVOICE_B2B => 'RetailOrder',
    RetailPaymentConfig::PAYMENT_METHOD_CREDIT_CARD => 'RetailOrder',
    RetailPaymentConfig::PAYMENT_METHOD_DIRECT_DEBIT => 'RetailOrder',
    RetailPaymentConfig::PAYMENT_METHOD_WHOLESALE_CASH => 'WholesaleOrder',
    RetailPaymentConfig::PAYMENT_METHOD_WHOLESALE_EC => 'WholesaleOrder',
    RetailPaymentConfig::PAYMENT_METHOD_WHOLESALE_CREDIT_CARD => 'WholesaleOrder',
    HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_METHOD_CREDIT_CARD_AUTHORIZE => 'WholesaleOrderCreditCardItemStates',
    HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_METHOD_PAY_PAL_AUTHORIZE => 'WholesaleOrderPayPalAuthorizationItemStates',
    HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_METHOD_SEPA_DIRECT_DEBIT => 'WholesaleOrderSepaDirectDebitItemStates',
    HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_METHOD_SEPA_DIRECT_DEBIT_B2B => 'WholesaleOrderSepaDirectDebitItemStates',
    HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_METHOD_SEPA_DIRECT_DEBIT_GUARANTEED => 'WholesaleOrderSepaDirectDebitGuaranteedItemStates',
    HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_METHOD_INVOICE => 'WholesaleOrderInvoiceItemStates',
    HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_METHOD_INVOICE_GUARANTEED => 'WholesaleOrderInvoiceGuaranteedItemStates',
    IntegraConstants::INTEGRA_NO_PAYMENT => 'Integra',
    HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_METHOD_CASH_ON_DELIVERY => 'WholesaleOrderPaymentOnDeliveryItemStates',
    HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_METHOD_EC_CARD_ON_DELIVERY => 'WholesaleOrderPaymentOnDeliveryItemStates',
    HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_METHOD_CREDIT_CARD_ON_DELIVERY => 'WholesaleOrderPaymentOnDeliveryItemStates',
];
$config[OmsConstants::OMS_RETAIL_ACCEPTED_STATE] = 'order.state.accepted';
$config[OmsConstants::OMS_RETAIL_DELIVERED_STATE] = 'order.state.delivered';
$config[OmsConstants::OMS_WHOLESALE_ACCEPTED_STATE] = 'ready for delivery';
$config[OmsConstants::OMS_WHOLESALE_PAYMENT_COMPLETE_STATES] = [
    'ready for closing',
    'closed',
    'declined',
    'deliveredToCustomer',
    'missing',
    'damaged',
    'canceled driver',
];

$config[OmsConstants::SALES_ORDER_RETRY_COUNTER] = 9;

$config[OmsConstants::OMS_ERROR_MAIL_RECIPIENTS] = [
    'oliver.gail@durst.de' => 'Oliver Gail',
];

$config[OmsConstants::OMS_ERROR_MAIL_SUBJECT] = 'Bestellung %s hängt in %s';

$config[HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_TYPE_MAP] = [
    HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_METHOD_CREDIT_CARD_AUTHORIZE => 'Kreditkarte',
    HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_METHOD_PAY_PAL_AUTHORIZE => 'Paypal',
    HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_METHOD_SEPA_DIRECT_DEBIT => 'SEPA',
    HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_METHOD_SEPA_DIRECT_DEBIT_B2B => 'SEPA',
    HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_METHOD_SEPA_DIRECT_DEBIT_GUARANTEED => 'SEPA (garantiert)',
    HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_METHOD_INVOICE => 'Rechnungskauf',
    HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_METHOD_INVOICE_GUARANTEED => 'Rechnungskauf (garantiert)',
];

$config[HeidelpayRestConstants::HEIDELPAY_REST_RECOVERABLE_ERRORS] = [
    CoreTimeoutAuthorizeException::ERROR_CODE,
    CoreTimeoutAuthorizeException::ERROR_CODE_ALTERNATIVE,
    CoreTimeoutChargeException::ERROR_CODE,
    CoreTimeoutCancellationException::ERROR_CODE,
    ConnectorAcquirerCurrentlyDownException::ERROR_CODE,
    ConnectorAcquirerCurrentlyDownException::ERROR_CODE_ALTERNATIVE,
];

$config[OmsConstants::RETAIL_PROCESS_NAME] = 'RetailOrder';

// ---------- Tour
$config[TourConstants::TOUR_ACTIVE_PROCESSES] = $config[OmsConstants::ACTIVE_PROCESSES];

/**
 * @deprecated
 */
$config[OmsConstants::OLD_PROCESSES_WHOLESALE_ORDER] = [
    'WholesaleOrderPayPalAuthorization',
    'WholesaleOrderSepaDirectDebit',
    'WholesaleOrderCreditCard',
];

// ---------- NewRelic
$config[NewRelicConstants::NEWRELIC_API_KEY] = null;

// ---------- Queue
$config[QueueConstants::QUEUE_SERVER_ID] = (gethostname()) ?: php_uname('n');
$config[QueueConstants::QUEUE_WORKER_INTERVAL_MILLISECONDS] = 1000;
$config[QueueConstants::QUEUE_WORKER_MAX_THRESHOLD_SECONDS] = 59;
$config[QueueConstants::QUEUE_WORKER_LOG_ACTIVE] = false;

/*
 * Queues can have different adapters and maximum worker number
 * QUEUE_ADAPTER_CONFIGURATION can have the array like this as an example:
 *
 *   'mailQueue' => [
 *       QueueConfig::CONFIG_QUEUE_ADAPTER => \Spryker\Client\RabbitMq\Model\RabbitMqAdapter::class,
 *       QueueConfig::CONFIG_MAX_WORKER_NUMBER => 5
 *   ],
 *
 *
 */
$config[QueueConstants::QUEUE_ADAPTER_CONFIGURATION_DEFAULT] = [
    QueueConfig::CONFIG_QUEUE_ADAPTER => RabbitMqAdapter::class,
    QueueConfig::CONFIG_MAX_WORKER_NUMBER => 1,
];

$config[QueueConstants::QUEUE_ADAPTER_CONFIGURATION] = [
    EventConstants::EVENT_QUEUE => [
        QueueConfig::CONFIG_QUEUE_ADAPTER => RabbitMqAdapter::class,
        QueueConfig::CONFIG_MAX_WORKER_NUMBER => 1,
    ],
];

$config[LogglyConstants::QUEUE_NAME] = 'loggly-log-queue';
$config[LogglyConstants::ERROR_QUEUE_NAME] = 'loggly-log-queue.error';

// ---------- Events
$config[EventConstants::LOGGER_ACTIVE] = false;

// ---------- EventBehavior
$config[EventBehaviorConstants::EVENT_BEHAVIOR_TRIGGERING_ACTIVE] = false;

// ---------- Customer
$config[CustomerConstants::CUSTOMER_SECURED_PATTERN] = '(^/login_check$|^(/en|/de)?/customer|^(/en|/de)?/wishlist)';
$config[CustomerConstants::CUSTOMER_ANONYMOUS_PATTERN] = '^/.*';

// ---------- Taxes
$config[TaxConstants::DEFAULT_TAX_RATE] = 19;
$config[TaxConstants::TAX_CORONA_DEADLINE] = new DateTime('2020-07-01');
$config[TaxConstants::TAX_CORONA_DEADLINE_END] = new DateTime('2020-12-31');
$config[TaxConstants::TAX_CORONA_TAX_RATE] = 16.0;

$config[FileSystemConstants::FILESYSTEM_SERVICE] = [];
$config[FlysystemConstants::FILESYSTEM_SERVICE] = $config[FileSystemConstants::FILESYSTEM_SERVICE];
$config[CmsGuiConstants::CMS_PAGE_PREVIEW_URI] = '/en/cms/preview/%d';

// ---------- Akeneo
$config[AkeneoPimConstants::HOST] = 'https://pim.durst.shop';

// ---------- Akeneo middleware connector
$mapDirectory = APPLICATION_ROOT_DIR . '/data/import/maps';
$config[AkeneoPimMiddlewareConnectorConstants::LOCALE_MAP_FILE_PATH] = $mapDirectory . '/locale_map.json';
$config[AkeneoPimMiddlewareConnectorConstants::ATTRIBUTE_MAP_FILE_PATH] = $mapDirectory . '/attribute_map.json';
$config[AkeneoPimMiddlewareConnectorConstants::SUPER_ATTRIBUTE_MAP_FILE_PATH] = $mapDirectory . '/super_attribute_map.json';
$config[AkeneoPimMiddlewareConnectorConstants::PRODUCT_MODEL_MAP_FILE_PATH] = $mapDirectory . '/product_models.json';
$config[AkeneoPimMiddlewareConnectorConstants::PRODUCT_MAP_FILE_PATH] = $mapDirectory . '/products.json';
$config[AkeneoPimMiddlewareConnectorConstants::FK_CATEGORY_TEMPLATE] = 1;
$config[AkeneoPimMiddlewareConnectorConstants::TAX_SET] = 'Germany Beverages';
$config[AkeneoPimMiddlewareConnectorConstants::LOCALES_FOR_IMPORT] = [
    'de_DE',
];
$config[AkeneoPimMiddlewareConnectorConstants::ACTIVE_STORES_FOR_PRODUCTS] = [
    'DE',
];
$config[AkeneoPimMiddlewareConnectorConstants::LOCALES_TO_PRICE_MAP] = [
    'de_DE' => [
        'currency' => 'EUR',
        'type' => 'DEFAULT',
        'store' => 'DE',
    ],
];

// ---------- Delivery Area
$config[DeliveryAreaConstants::TIME_SLOT_DATE_TIME_FORMAT] = DateTime::RFC3339;
$config[DeliveryAreaConstants::TIME_SLOT_TIME_FORMAT] = 'H:i';
$config[DeliveryAreaConstants::DELIVERY_AREA_CSV_FILE_TMP_PATH] = sprintf('%s/%s/%s', APPLICATION_ROOT_DIR, 'data', 'export');

$config[DeliveryAreaConstants::MAX_CUSTOMERS_AND_PRODUCTS_VALIDATION_STATE_BLACKLIST] = [
    'order.state.declined',
    'order.state.confirmed',
    'order.state.closed',
    'invalid',
    /* START: heidelpay invoice secure state added to blacklist, OG, DSB-703 */
    'heidelpay invoice failed',
    /* END: heidelpay invoice secure state added to blacklist, OG, DSB-703 */
    'customer invalid',
    'confirmation failed',
    'authorization canceled',
    'ready to send invalid email',
    'confirm rollback',
    'capture failed',
    /* START: states from a canceled order, OG */
    'start cancel',
    'refund cancel authorization',
    'refund cancel payment',
    'recalculate cancellation',
    'revert tour',
    'send cancel mail',
    'integra cancel',
    'persist cancellation',
    'canceled user',
    'continue tour',
    'mark cancelled',
    'canceled driver',
    'canceled driver early',
    /* END: states from a canceled order, OG */
    'mark graphmasters order cancelled after customer check',
    'mark graphmasters order cancelled after capture',
    'mark graphmasters order cancelled after confirmation',
];

// ---------- App Rest API
// schema file path
$config[AppRestApiConstants::SCHEMA_FOLDER_PATH] = APPLICATION_ROOT_DIR . '/config/Yves/schemas';
$config[AppRestApiConstants::SCHEMA_TIME_SLOT_REQUEST] = '/time_slot_request.json';
$config[AppRestApiConstants::SCHEMA_TIME_SLOT_RESPONSE] = '/time_slot_response.json';
$config[AppRestApiConstants::SCHEMA_BRANCH_REQUEST] = '/branch_request.json';
$config[AppRestApiConstants::SCHEMA_BRANCH_RESPONSE] = '/branch_response.json';
$config[AppRestApiConstants::SCHEMA_CITY_REQUEST] = '/city_request.json';
$config[AppRestApiConstants::SCHEMA_CITY_RESPONSE] = '/city_response.json';
$config[AppRestApiConstants::SCHEMA_ORDER_REQUEST] = '/order_request.json';
$config[AppRestApiConstants::SCHEMA_ORDER_RESPONSE] = '/order_response.json';
$config[AppRestApiConstants::SCHEMA_PAYMENT_STATUS_REQUEST] = '/payment_status_request.json';
$config[AppRestApiConstants::SCHEMA_PAYMENT_STATUS_RESPONSE] = '/payment_status_response.json';
$config[AppRestApiConstants::SCHEMA_VOUCHER_REQUEST] = '/voucher_request.json';
$config[AppRestApiConstants::SCHEMA_VOUCHER_RESPONSE] = '/voucher_response.json';
$config[AppRestApiConstants::SCHEMA_CATEGORY_RESPONSE] = '/category_response.json';
$config[AppRestApiConstants::SCHEMA_DRIVER_APP_LOGIN_REQUEST] = '/driver_app_login_request.json';
$config[AppRestApiConstants::SCHEMA_DRIVER_APP_LOGIN_RESPONSE] = '/driver_app_login_response.json';
$config[AppRestApiConstants::SCHEMA_DRIVER_APP_LOGOUT_REQUEST] = '/driver_app_logout_request.json';
$config[AppRestApiConstants::SCHEMA_DRIVER_APP_LOGOUT_RESPONSE] = '/driver_app_logout_response.json';
$config[AppRestApiConstants::SCHEMA_DRIVER_APP_CLOSE_ORDER_REQUEST] = '/driver_app_close_order_request.json';
$config[AppRestApiConstants::SCHEMA_DRIVER_APP_CLOSE_ORDER_RESPONSE] = '/driver_app_close_order_response.json';
$config[AppRestApiConstants::SCHEMA_DRIVER_APP_DEPOSIT_REQUEST] = '/driver_app_deposit_request.json';
$config[AppRestApiConstants::SCHEMA_DRIVER_APP_DEPOSIT_RESPONSE] = '/driver_app_deposit_response.json';
$config[AppRestApiConstants::SCHEMA_DRIVER_APP_GTIN_REQUEST] = '/driver_app_gtin_request.json';
$config[AppRestApiConstants::SCHEMA_DRIVER_APP_GTIN_RESPONSE] = '/driver_app_gtin_response.json';
$config[AppRestApiConstants::SCHEMA_DRIVER_APP_TOUR_REQUEST] = '/driver_app_tour_request.json';
$config[AppRestApiConstants::SCHEMA_DRIVER_APP_TOUR_RESPONSE] = '/driver_app_tour_response.json';
$config[AppRestApiConstants::SCHEMA_DRIVER_APP_ORDER_REQUEST] = '/driver_app_order_request.json';
$config[AppRestApiConstants::SCHEMA_DRIVER_APP_ORDER_RESPONSE] = '/driver_app_order_response.json';
$config[AppRestApiConstants::SCHEMA_DRIVER_APP_BRANCHES_REQUEST] = '/driver_app_branches_request.json';
$config[AppRestApiConstants::SCHEMA_DRIVER_APP_BRANCHES_RESPONSE] = '/driver_app_branches_response.json';
$config[AppRestApiConstants::SCHEMA_DRIVER_APP_LATEST_RELEASE_REQUEST] = '/driver_app_latest_release_request.json';
$config[AppRestApiConstants::SCHEMA_DRIVER_APP_LATEST_RELEASE_RESPONSE] = '/driver_app_latest_release_response.json';
$config[AppRestApiConstants::SCHEMA_DRIVER_APP_DOWNLOAD_LATEST_RELEASE_REQUEST] = '/driver_app_download_latest_release_request.json';
$config[AppRestApiConstants::SCHEMA_CITY_MERCHANT_REQUEST_V1] = '/city-merchants/city_merchants_request_v1.json';
$config[AppRestApiConstants::SCHEMA_CITY_MERCHANT_REQUEST_V2] = '/city-merchants/city_merchants_request_v2.json';
$config[AppRestApiConstants::SCHEMA_CITY_MERCHANT_RESPONSE] = '/city-merchants/city_merchants_response.json';
$config[AppRestApiConstants::SCHEMA_MERCHANT_PRODUCT_REQUEST] = '/merchant-product/merchant_product_request.json';
$config[AppRestApiConstants::SCHEMA_MERCHANT_PRODUCT_RESPONSE] = '/merchant-product/merchant_product_response.json';
$config[AppRestApiConstants::SCHEMA_MERCHANT_PRODUCTS_REQUEST_V1] = '/merchant-product/merchant_products_request_v1.json';
$config[AppRestApiConstants::SCHEMA_MERCHANT_PRODUCTS_REQUEST_V2] = '/merchant-product/merchant_products_request_v2.json';
$config[AppRestApiConstants::SCHEMA_MERCHANT_PRODUCTS_REQUEST_V3] = '/merchant-product/merchant_products_request_v2.json';
$config[AppRestApiConstants::SCHEMA_MERCHANT_PRODUCTS_RESPONSE_V1] = '/merchant-product/merchant_products_response_v1.json';
$config[AppRestApiConstants::SCHEMA_MERCHANT_PRODUCTS_RESPONSE_V2] = '/merchant-product/merchant_products_response_v2.json';
$config[AppRestApiConstants::SCHEMA_MERCHANT_PRODUCTS_RESPONSE_V3] = '/merchant-product/merchant_products_response_v3.json';
$config[AppRestApiConstants::SCHEMA_MERCHANT_TIME_SLOT_REQUEST] = '/merchant_timeslots_request.json';
$config[AppRestApiConstants::SCHEMA_MERCHANT_TIME_SLOT_RESPONSE] = '/merchant_timeslots_response.json';
$config[AppRestApiConstants::SCHEMA_EVALUATE_TIME_SLOT_REQUEST] = '/evaluate_timeslots_request.json';
$config[AppRestApiConstants::SCHEMA_EVALUATE_TIME_SLOT_RESPONSE] = '/evaluate_timeslots_response.json';
$config[AppRestApiConstants::SCHEMA_OVERVIEW_REQUEST] = '/overview_request.json';
$config[AppRestApiConstants::SCHEMA_OVERVIEW_RESPONSE] = '/overview_response.json';
$config[AppRestApiConstants::SCHEMA_DISCOUNT_REQUEST] = '/discount_request.json';
$config[AppRestApiConstants::SCHEMA_DISCOUNT_RESPONSE] = '/discount_response.json';
$config[AppRestApiConstants::SCHEMA_DELIVERY_AREA_REQUEST] = '/delivery_area_request.json';
$config[AppRestApiConstants::SCHEMA_DELIVERY_AREA_RESPONSE] = '/delivery_area_response.json';
$config[AppRestApiConstants::SCHEMA_DEPOSIT_PICKUP_CREATE_INQUIRY_REQUEST] = '/deposit_pickup_create_inquiry_request.json';
$config[AppRestApiConstants::SCHEMA_DEPOSIT_PICKUP_CREATE_INQUIRY_RESPONSE] = '/deposit_pickup_create_inquiry_response.json';
$config[AppRestApiConstants::SCHEMA_DRIVER_APP_CANCEL_ORDER_REQUEST] = '/driver_app_cancel_order_request.json';
$config[AppRestApiConstants::SCHEMA_DRIVER_APP_CANCEL_ORDER_RESPONSE] = '/driver_app_cancel_order_response.json';

// ---------- analytics logging
$config[AppRestApiConstants::ANALYTICS_BRANCH_LOG_FILE_PATH_YVES] = $baseLogFilePath . '/YVES/analytics_branch.log';
$config[AppRestApiConstants::ANALYTICS_TIME_SLOT_LOG_FILE_PATH_YVES] = $baseLogFilePath . '/YVES/analytics_time_slot.log';
$config[AppRestApiConstants::ANALYTICS_MERCHANT_TIME_SLOT_LOG_FILE_PATH_YVES] = $baseLogFilePath . '/YVES/analytics_merchant_time_slot.log';
$config[AppRestApiConstants::ANALYTICS_OVERVIEW_LOG_FILE_PATH_YVES] = $baseLogFilePath . '/YVES/analytics_overview.log';

// ---------- media
$config[AppRestApiConstants::MEDIA_SERVER_HOST] = 'https://media.durst.shop';
$config[AppRestApiConstants::FALLBACK_IMAGE_PRODUCT] = 'fallbacks/durst_bottle_fallback.png';
$config[AppRestApiConstants::UPLOAD_BRANCH_FOLDER_HOST] = 'http://merchant.de.demoshop.local';
$config[AppRestApiConstants::UPLOAD_BRANCH_FOLDER_DIR] = '/assets/upload/branch/';
$config[AppRestApiConstants::UPLOAD_PAYMENT_METHOD_DIR] = '/assets/payment/';

// ---------- image scaling
$config[AppRestApiConstants::IMAGE_SCALING_PATH] = '/resized';
$config[AppRestApiConstants::IMAGE_SCALING_PATH_THUMB] = $config[AppRestApiConstants::IMAGE_SCALING_PATH] . '/150';
$config[AppRestApiConstants::IMAGE_SCALING_PATH_BIG] = $config[AppRestApiConstants::IMAGE_SCALING_PATH] . '/1200';

// ---------- timeslot api settings
$config[AppRestApiConstants::API_TIME_SLOTS_MAX] = 6;
$config[AppRestApiConstants::API_TIME_SLOTS_ITEMS_PER_SLOT] = 1;
$config[AppRestApiConstants::API_TIME_SLOTS_DAY_LIMIT] = 5;
$config[AppRestApiConstants::API_GM_TIME_SLOTS_DAY_LIMIT] = 5;

// ---------- Terms of Service
$config[TermsOfServiceConstants::CUSTOMER_TERMS_NAME] = 'customer_terms';

// ---------- Merchant Price
$config[MerchantPriceConstants::DEFAULT_COUNTRY_ISO_3_CODE] = 'DEU';
$config[MerchantPriceConstants::COUNT_SOLD_ITEMS] = "-1 month";

// ---------- Mail
$config[MailConstants::MAIL_ASSETS_BASE_URL] = 'https://media.durst.shop';
$config[MailConstants::MAIL_CUSTOMER_SURVEY_URL] = 'https://www.durst.de/kundenfeedback?CH=%s&ZIP=%s&MID=%d&DD=%s&OV=%s';
$config[MailConstants::MAIL_CUSTOMER_SURVEY_HAPPINESS] = ['positive', 'neutral', 'negative'];
$config[MailConstants::MAIL_CUSTOMER_PAYMENT_FEEDBACK_URL] = 'https://www.durst.de/paymentfeedback/?OID=%s';

$config[MailConstants::MAIL_DURST_COMPANY_NAME] = 'durst.company.name';
$config[MailConstants::MAIL_DURST_COMPANY_STREET] = 'durst.company.street';
$config[MailConstants::MAIL_DURST_COMPANY_CITY] = 'durst.company.city';
$config[MailConstants::MAIL_DURST_COMPANY_WEB] = 'durst.company.web';
$config[MailConstants::MAIL_DURST_COMPANY_EMAIL] = 'durst.company.email';
$config[MailConstants::MAIL_DURST_COMPANY_VAT_ID] = 'durst.company.vatid';
$config[MailConstants::MAIL_DURST_COMPANY_BIO] = 'durst.company.bio';
$config[MailConstants::MAIL_DURST_COMPANY_JURISDICTION] = 'durst.company.jurisdiction';
$config[MailConstants::MAIL_DURST_COMPANY_MANAGEMENT] = 'durst.company.management';

$config[MailConstants::MAIL_FOOTER_BANNER_IMG] = 'durst.mail.banner_img';
$config[MailConstants::MAIL_FOOTER_BANNER_LINK] = 'durst.mail.banner_link';
$config[MailConstants::MAIL_FOOTER_BANNER_ALT] = 'durst.mail.banner_alt';
$config[MailConstants::MAIL_FOOTER_BANNER_CTA] = 'durst.mail.banner_cta';

$config[MailConstants::MAIL_MERCHANT_CENTER_BASE_URL] = 'http://merchant.de.durst.local';

$config[MailConstants::MAIL_RECIPIENT_DEVELOPER] = [
    'email' => 'developer@durst.shop',
    'name' => 'Durst Developer'
];

$config[MailConstants::MAIL_RECIPIENT_SERVICE] = [
    'email' => 'service@durst.shop',
    'name' => 'Durst'
];

$config[MailConstants::MAIL_CANCEL_ORDER_BASE_URL] = 'http://www.de.durst.local/cancel-order/cancel?t=%s';

// ---------- Product Exporter path
$config[ProductConstants::PRODUCT_EXPORTER_PATH] = APPLICATION_ROOT_DIR . '/public/Zed/assets/downloads/durst_haendler_produkt_export.csv';

// ---------- Tour Exporter path
$config[TourConstants::TOUR_EXPORTER_PATH] = APPLICATION_ROOT_DIR . '/public/Zed/assets/downloads/tour_export.csv';

// ---------- Tour Durst ILN
$config[TourConstants::DURST_ILN] = '4399902370295';

// ---------- Tour EDIFACT D96a Test Run
$config[TourConstants::EDIFACT_TESTRUN] = true;

// ---------- Tour Export: PHP path to executable
$config[TourConstants::PHP_PATH_FOR_CONSOLE] = '/usr/local/bin/php';

// ---------- Wholesale Tour State Machine: initial state
$config[TourConstants::TOUR_INITIAL_STATE] = TourConstants::TOUR_STATE_NEW;

// ---------- Concrete Tour Listing: Earliest allowed date for filtering
$config[TourConstants::CONCRETE_TOUR_FILTERING_EARLIEST_ALLOWED_DATE] = '2020-04-01';

// ---------- Tour EDIFACT cURL settings
$config[TourConstants::EDI_CLIENT_CURL_OPTIONS] = [
    'curl' => [
        CURLOPT_SSL_CIPHER_LIST => 'DEFAULT@SECLEVEL=1'
    ]
];

// ---------- Software-Methods
$config[SoftwarePackageConstants::SOFTWARE_FEATURE_ALLOW_COMMENTS] = 'allow_order_comments';

// ---------- time slot
$config[DeliveryAreaConstants::CONCRETE_TIME_SLOT_CREATION_LIMIT] = '+14day';

// ---------- docker script for jenkins execution
$config[SetupConstants::DOCKER_SCRIPT_PATH] = '/usr/local/bin/docker_exec.sh';
$config[SetupConstants::PHP_BINARY_PATH] = '/usr/local/bin/php';

// ---------- Unzer REST API (formerly Heidelpay)
$config[HeidelpayRestConstants::HEIDELPAY_REST_PRIVATE_KEY] = 's-priv-2a10tPI14ymhn6vUNfuzFC0cTujELyYz';
$config[HeidelpayRestConstants::HEIDELPAY_REST_PUBLIC_KEY] = 's-pub-2a10ks8JC1WJhEFCWfqmaBOWxeN9nebk';
$config[HeidelpayRestConstants::HEIDELPAY_REST_API_VERSION] = 'v1';
$config[HeidelpayRestConstants::HEIDELPAY_REST_HOST] = 'api.heidelpay.com';
$config[HeidelpayRestConstants::HEIDELPAY_BASE_URL] = 'https://' . $config[HeidelpayRestConstants::HEIDELPAY_REST_HOST] . '/' . $config[HeidelpayRestConstants::HEIDELPAY_REST_API_VERSION];
$config[HeidelpayRestConstants::HEIDELPAY_REST_IS_DEBUG] = true;
$config[HeidelpayRestConstants::HEIDELPAY_LOCALE] = 'de_DE';
$config[HeidelpayRestConstants::HEIDELPAY_RETURN_URL] = 'https://media.durst.shop/assets/paypal-redirect.html';
$config[HeidelpayRestConstants::HEIDELPAY_REST_DEBUG_LOG_PATH] = $baseLogFilePath . '/ZED/heidelpay_rest_debug.log';
$config[HeidelpayRestConstants::HEIDELPAY_REST_FALLBACK_ERROR_MESSAGE] = 'Deine Bezahlung war leider nicht erfolgreich. Wir konnten keine Freigabe deines Rechnungsbetrags sicherstellen. Bitte versuche es noch einmal.';
$config[HeidelpayRestConstants::HEIDELPAY_REST_SEPA_MANDATE_URL] = 'https://media.durst.shop/assets/sepa-mandate-text.html';
$config[HeidelpayRestConstants::HEIDELPAY_REST_START_DATE_BRANCH_SPECIFIC_KEYS] = '2020-04-01';

// ---------- License Manager
$config[SoftwarePackageConstants::LICENSE_KEY_KEY] = '5RdRDCmG89DooltnMlUG';
$config[SoftwarePackageConstants::LICENSE_KEY_VI] = '2Ve2W2g9ANKpvQNXuP3w';
$config[SoftwarePackageConstants::LICENSE_KEY_METHOD] = 'AES-256-CBC';

// ---------- Sales
$config[SalesConstants::SALES_SIGNATURE_IMAGE_PATH] = APPLICATION_ROOT_DIR . '/public/Zed/assets/upload/signatures';

// ---------- Pdf
$config[PdfConstants::PDF_SAVE_PATH] = APPLICATION_ROOT_DIR . '/public/Zed/assets/upload/invoices';
$config[PdfConstants::PDF_MAIL_TO_PDF_TEMPLATE] = '@Oms/Mail/merchant-order-invoice-pdf.html.twig';
$config[PdfConstants::PDF_ASSETS_PATH] = APPLICATION_ROOT_DIR . '/public/Zed/pdf';

// ---------- JWT Driver App
$config[AuthConstants::JWT_ISSUER] = 'http://www.durst.de';
$config[AuthConstants::JWT_AUDIENCE] = 'http://www.durst.de';

// ---------- Driver App
$config[DriverAppConfig::DRIVER_APP_UPLOAD_PATH] = APPLICATION_ROOT_DIR . '/public/Zed/assets/upload/driver-app';

// ---------- Easybill
$config[EasybillConstants::INVOICE_DELAY_QUEUE] = EasybillConstants::INVOICE_DELAY_QUEUE;
$config[EasybillConstants::INVOICE_DELAY_ERROR_QUEUE] = EasybillConstants::INVOICE_DELAY_ERROR_QUEUE;
$config[EasybillConfig::INVOICE_DELAY_QUEUE_CHUNK_SIZE] = 10;

// ---------- Graphhopper
$config[GraphhopperConstants::GRAPHHOPPER_API_KEY] = '9aa1e03f-960a-4894-aa5d-7a65f411fe67';
$config[GraphhopperConstants::GRAPHHOPPER_LOCALE] = strtolower($CURRENT_STORE);
$config[GraphhopperConstants::GRAPHHOPPER_GEOCODING_PROVIDER] = 'opencagedata';

// ---------- Easybill
$config[EasybillConfig::EASYBILL_API_URL] = 'https://api.easybill.de/rest/v1';
$config[EasybillConfig::EASYBILL_API_KEY] = 'Gbj4ryaH61jF6rHW0DTPm00VULH2Jp3yhHEcksc7R85DNUQV5asPYLM7am02fRK0';
$config[EasybillConfig::EASYBILL_EMAIL] = 'mathias.bicker+1@durst.shop';

// ---------- Google Api
$config[GoogleApiConstants::GOOGLE_API_GEOCODING_KEY] = 'AIzaSyCdFAaT_yNShP0zIskQEDdQtJStbKdOyNE';

// ---------- Billing
$config[BillingConstants::BILLING_PERIOD_GENERATE_DAYS_IN_ADVANCE] = '1 day';
$config[BillingConstants::BILLING_PERIOD_ZIP_ARCHIVE_TEMP_PATH] = sprintf('%s/data/billing', APPLICATION_ROOT_DIR);

// ---------- Invoice
$config[InvoiceConfig::INVOICE_REFERENCE_PREFIX] = 'TEST';
$config[InvoiceConfig::INVOICE_REFERENCE_SEPARATOR] = '-';

// ---------- Realax Invoice License Keys
$config[AccountingConstants::INVOICE_LICENSE_FIXED] = '8402';
$config[AccountingConstants::INVOICE_LICENSE_FIXED_REDUCED] = '8402-16';
$config[AccountingConstants::INVOICE_LICENSE_VARIABLE] = '8404';
$config[AccountingConstants::INVOICE_LICENSE_VARIABLE_REDUCED] = '8404-16';
$config[AccountingConstants::INVOICE_MARKETING_FIXED] = '8407';
$config[AccountingConstants::INVOICE_MARKETING_FIXED_REDUCED] = '8407-16';
$config[AccountingConstants::INVOICE_MARKETING_VARIABLE] = '8408';
$config[AccountingConstants::INVOICE_MARKETING_VARIABLE_REDUCED] = '8408-16';
$config[AccountingConstants::REALAX_DELIMITER] = ';';
$config[AccountingConstants::REALAX_CSV_LINE_FORMAT] = "%s;\n";
$config[AccountingConstants::REALAX_EXPORT_PATH] = APPLICATION_ROOT_DIR . '/public/Zed/assets/downloads/realax';
$config[AccountingConstants::REALAX_RECIPIENTS] = [
    'oliver.gail@durst.shop' => 'Oliver Gail',
];
$config[AccountingConstants::PROCESS_TIMEOUT] = 6000;
$config[AccountingConstants::REALAX_CORONA_TAX_REDUCTION_MONTH] = [
    7,
    8,
    9,
    10,
    11,
    12,
];
$config[AccountingConstants::REALAX_CORONA_TAX_REDUCTION_YEAR] = [
    2020,
];
$config[AccountingConstants::REALAX_NORMAL_TAX_RATE] = 1.19;
$config[AccountingConstants::REALAX_CORONA_TAX_RATE] = 1.16;
$config[AccountingConstants::OMS_WHOLESALE_PAYMENT_ACCOUNTING_STATES] = [
    'deliveredToCustomer',
];

// ---------- Product export (e.g. GBZ)
$config[ProductExportConstants::BATCH_SIZE] = 10;
$config[ProductExportConstants::FILE_PATH] = sprintf('%s/data/product_export', APPLICATION_ROOT_DIR);

// ---------- Price import
$config[PriceImportConstants::UPLOAD_PRICE_IMPORT_FOLDER] = sprintf('%s/data/price-import', APPLICATION_ROOT_DIR);

// ---------- Integra
$config[IntegraConstants::INTEGRA_CSV_FILE_TMP_PATH] = sprintf(
    '%s/%s/%s/%s',
    APPLICATION_ROOT_DIR,
    'data',
    'export',
    'integra'
);
$config[IntegraConstants::INTEGRA_LOG_LEVEL] = IntegraConstants::INTEGRA_LOG_LEVEL_INFO;
$config[IntegraConstants::INTEGRA_ENCRYPTION_CIPHER_METHOD] = 'AES-256-CBC';
$config[IntegraConstants::INTEGRA_ENCRYPTION_IV] = 'ewDDOLt+D+fViA==';
$config[IntegraConstants::INTEGRA_ENCRYPTION_KEY] = 'Eykjzwv+VBp+oomSUayvVYSvVfTyaOAANr72hYzPVEhP0/C\
XGqCGgyzomWMnO+OshdwoLI2fUGkEzWrUi1qMruLHTVXsyyevbxdHn+ZkMUyceuBS83XnBQvNyVP7GC1hc7zoFWTeEfDwhaVEzTzL\
3fWsLgfH49nLuzn01Rhkrazamcl8cnJTDpiGQzQKE4wO3lHhJE+6FoBjw47IqHNu2BkmOSzGnIZgUbo9xrDRBi4kNZ+oaNrzuTMhz7\
kxiOgJxgTLiZ+WO/g2NBtFaiASjmlWjbDqPQyACu1Ar8Y6zbEWj7dftm1NcZw1w2uebk7yfpN5bCXZ1FIsiu/4SMG1Sg==';
$config[IntegraConstants::PDF_DELIVERY_NOTE_SAVE_PATH] = APPLICATION_ROOT_DIR . '/public/Zed/assets/upload/integra/deliverynotes';

// ---------- Graphmasters
$config[GraphMastersConstants::GRAPHMASTERS_API_KEY] = 'TFdIakEyOHJlZU5sVW9Da0ZHc3ZQVU40WUUxNzVlY3Y6b0FabUVLVjI5RlRsOFdDM1QtM1pIOHdfbm5ZSVRtREVOcTJNS0U5aWxBODdnTTZrVzNVTXdWUmlFd05rUFZwdg==';
$config[GraphMastersConstants::GRAPHMASTERS_BASE_URL] = 'https://obncmw-middlewares.nunav.net/';
$config[GraphMastersConstants::GRAPHMASTERS_DAYS_IN_ADVANCE] = 5;
$config[GraphmastersConstants::GRAPHMASTERS_TOUR_FILTERING_EARLIEST_ALLOWED_DATE] = '2021-10-01';

// ---------- Campaign setting, copied from AppRestApi
$config[CampaignConstants::MEDIA_SERVER_HOST] = $config[AppRestApiConstants::MEDIA_SERVER_HOST];
$config[CampaignConstants::FALLBACK_IMAGE_PRODUCT] = $config[AppRestApiConstants::FALLBACK_IMAGE_PRODUCT];
$config[CampaignConstants::IMAGE_SCALING_PATH] = $config[AppRestApiConstants::IMAGE_SCALING_PATH];
$config[CampaignConstants::IMAGE_SCALING_PATH_THUMB] = $config[AppRestApiConstants::IMAGE_SCALING_PATH_THUMB];
$config[CampaignConstants::IMAGE_SCALING_PATH_BIG] = $config[AppRestApiConstants::IMAGE_SCALING_PATH_BIG];
$config[CampaignConstants::DEEP_LINK_URL] = 'https://www.durst.shop/start?branchCode=%s&productSku=%s';
$config[CampaignConstants::CAMPAIGN_DISCOUNT_NAME] = 'Aktion';

// ---------- Cancel orders
$config[CancelOrderConstants::CANCEL_LEAD_TIME] = '-10min';
$config[CancelOrderConstants::ISSUER_FRIDGE] = CancelOrderConstants::FRIDGE;
$config[CancelOrderConstants::ISSUER_CUSTOMER] = CancelOrderConstants::CUSTOMER;
$config[CancelOrderConstants::ISSUER_DRIVER] = CancelOrderConstants::DRIVER;
$config[CancelOrderConstants::POSSIBLE_ISSUERS] = [
    $config[CancelOrderConstants::ISSUER_FRIDGE],
    $config[CancelOrderConstants::ISSUER_CUSTOMER],
    $config[CancelOrderConstants::ISSUER_DRIVER]
];
$config[CancelOrderConstants::FRIDGE_CANCEL_URL] = sprintf(
    '/%s/%s/%s?%s&%s',
    'cancel-order',
    'index',
    'cancel',
    't=%s',
    'redirect=%s'
);

// ---------- Glue
$config[GlueApplicationConstants::GLUE_APPLICATION_DOMAIN] = '';
$config[GlueApplicationConstants::GLUE_APPLICATION_CORS_ALLOW_ORIGIN] = '*';
$config[GlueApplicationConstants::GLUE_APPLICATION_REST_DEBUG] = true;

// ---------- Oauth2
$config[OauthConstants::PRIVATE_KEY_PATH] = 'file://' . APPLICATION_ROOT_DIR . '/config/Glue/dev_private.key';
$config[OauthConstants::PUBLIC_KEY_PATH] = 'file://' . APPLICATION_ROOT_DIR . '/config/Glue/dev_public.key';
$config[OauthConstants::ENCRYPTION_KEY] = 'KNrGAD8kh3LAKjg7+6/PjDmRHOJ/IbMJjoFPRksU15E=';
$config[OauthConstants::OAUTH_CLIENT_IDENTIFIER] = 'durst-glue-api';
$config[OauthConstants::OAUTH_CLIENT_SECRET] = 'A$Hf$o?GDykd5qmN';
