<?php

/**
 * This is the global runtime configuration for Yves and Generated_Yves_Zed in a production environment.
 */

use Monolog\Logger;
use Pyz\Shared\Accounting\AccountingConstants;
use Pyz\Shared\AppRestApi\AppRestApiConstants;
use Pyz\Shared\GoogleApi\GoogleApiConstants;
use Pyz\Shared\HeidelpayRest\HeidelpayRestConstants;
use Pyz\Shared\Invoice\InvoiceConfig;
use Pyz\Shared\Log\LogConstants;
use Pyz\Shared\Mail\MailConstants;
use Pyz\Shared\Oms\OmsConstants;
use Pyz\Shared\ProductExport\ProductExportConstants;
use Pyz\Shared\Tour\TourConstants;
use Pyz\Shared\WebProfiler\WebProfilerConstants;
use Spryker\Shared\Acl\AclConstants;
use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Shared\Collector\CollectorConstants;
use Spryker\Shared\Config\ConfigConstants;
use Spryker\Shared\ErrorHandler\ErrorHandlerConstants;
use Spryker\Shared\ErrorHandler\ErrorRenderer\WebExceptionErrorRenderer;
use Spryker\Shared\Event\EventConstants;
use Spryker\Shared\Kernel\KernelConstants;
use Spryker\Shared\Kernel\Store;
use Spryker\Shared\Propel\PropelConstants;
use Spryker\Shared\PropelOrm\PropelOrmConstants;
use Spryker\Shared\PropelQueryBuilder\PropelQueryBuilderConstants;
use Spryker\Shared\RabbitMq\RabbitMqConstants;
use Spryker\Shared\Search\SearchConstants;
use Spryker\Shared\Session\SessionConstants;
use Spryker\Shared\Setup\SetupConstants;
use Spryker\Shared\Storage\StorageConstants;
use Spryker\Shared\Twig\TwigConstants;
use Spryker\Shared\ZedNavigation\ZedNavigationConstants;
use Spryker\Shared\ZedRequest\ZedRequestConstants;

$CURRENT_STORE = Store::getInstance()->getStoreName();

// ---------- General environment
$config[KernelConstants::SPRYKER_ROOT] = APPLICATION_ROOT_DIR . '/vendor/spryker';
$config[KernelConstants::STORE_PREFIX] = 'PROD';
$config[ApplicationConstants::ENABLE_APPLICATION_DEBUG] = false;
$config[WebProfilerConstants::ENABLE_WEB_PROFILER]
    = $config[ConfigConstants::ENABLE_WEB_PROFILER]
    = false;

$config[ApplicationConstants::ZED_SSL_ENABLED] = true;
$config[ApplicationConstants::YVES_SSL_ENABLED] = true;

// ---------- Propel
$config[PropelConstants::PROPEL_DEBUG] = false;
$config[PropelOrmConstants::PROPEL_SHOW_EXTENDED_EXCEPTION] = false;
$config[PropelConstants::ZED_DB_USERNAME] = 'durst';
$config[PropelConstants::ZED_DB_PASSWORD] = 'zuTaezoo*B2Ua{paec9wei$X';
$config[PropelConstants::ZED_DB_HOST] = 'postgres';
$config[PropelConstants::ZED_DB_PORT] = 5432;
$config[PropelConstants::USE_SUDO_TO_MANAGE_DATABASE] = false;
$config[PropelConstants::ZED_DB_ENGINE] = $config[PropelConstants::ZED_DB_ENGINE_PGSQL];
$config[PropelQueryBuilderConstants::ZED_DB_ENGINE] = $config[PropelConstants::ZED_DB_ENGINE_PGSQL];

// ---------- Redis
$config[StorageConstants::STORAGE_REDIS_PROTOCOL] = 'tcp';
$config[StorageConstants::STORAGE_REDIS_HOST] = 'redis';
$config[StorageConstants::STORAGE_REDIS_PORT] = '6379';
$config[StorageConstants::STORAGE_REDIS_PASSWORD] = false;
$config[StorageConstants::STORAGE_REDIS_DATABASE] = 0;

// ---------- RabbitMQ
$config[RabbitMqConstants::RABBITMQ_HOST] = 'rabbitmq';
$config[RabbitMqConstants::RABBITMQ_PORT] = '5672';
$config[RabbitMqConstants::RABBITMQ_PASSWORD] = 'mate20mg';

$config[RabbitMqConstants::RABBITMQ_API_HOST] = 'rabbitmq';
$config[RabbitMqConstants::RABBITMQ_API_PORT] = '15672';
$config[RabbitMqConstants::RABBITMQ_API_USERNAME] = 'spryker';
$config[RabbitMqConstants::RABBITMQ_API_PASSWORD] = 'mate20mg';

// ---------- Elasticsearch
$ELASTICA_HOST = 'elasticsearch';
$config[SearchConstants::ELASTICA_PARAMETER__HOST] = $ELASTICA_HOST;
$ELASTICA_TRANSPORT_PROTOCOL = 'http';
$config[SearchConstants::ELASTICA_PARAMETER__TRANSPORT] = $ELASTICA_TRANSPORT_PROTOCOL;
$ELASTICA_PORT = '9200';
$config[SearchConstants::ELASTICA_PARAMETER__PORT] = $ELASTICA_PORT;
$ELASTICA_AUTH_HEADER = '';
$config[SearchConstants::ELASTICA_PARAMETER__AUTH_HEADER] = $ELASTICA_AUTH_HEADER;
$ELASTICA_INDEX_NAME = null;// Store related config
$config[SearchConstants::ELASTICA_PARAMETER__INDEX_NAME] = $ELASTICA_INDEX_NAME;
$config[CollectorConstants::ELASTICA_PARAMETER__INDEX_NAME] = $ELASTICA_INDEX_NAME;
$ELASTICA_DOCUMENT_TYPE = 'page';
$config[SearchConstants::ELASTICA_PARAMETER__DOCUMENT_TYPE] = $ELASTICA_DOCUMENT_TYPE;
$config[CollectorConstants::ELASTICA_PARAMETER__DOCUMENT_TYPE] = $ELASTICA_DOCUMENT_TYPE;
$ELASTICA_PARAMETER__EXTRA = [];
$config[SearchConstants::ELASTICA_PARAMETER__EXTRA] = $ELASTICA_PARAMETER__EXTRA;

// ---------- Session
$config[SessionConstants::YVES_SESSION_COOKIE_SECURE] = true;
$config[SessionConstants::YVES_SESSION_REDIS_PROTOCOL] = $config[StorageConstants::STORAGE_REDIS_PROTOCOL];
$config[SessionConstants::YVES_SESSION_REDIS_HOST] = $config[StorageConstants::STORAGE_REDIS_HOST];
$config[SessionConstants::YVES_SESSION_REDIS_PORT] = $config[StorageConstants::STORAGE_REDIS_PORT];
$config[SessionConstants::YVES_SESSION_REDIS_PASSWORD] = $config[StorageConstants::STORAGE_REDIS_PASSWORD];
$config[SessionConstants::YVES_SESSION_REDIS_DATABASE] = 1;
$config[SessionConstants::ZED_SESSION_COOKIE_SECURE] = true;
$config[SessionConstants::ZED_SESSION_REDIS_PROTOCOL] = $config[SessionConstants::YVES_SESSION_REDIS_PROTOCOL];
$config[SessionConstants::ZED_SESSION_REDIS_HOST] = $config[SessionConstants::YVES_SESSION_REDIS_HOST];
$config[SessionConstants::ZED_SESSION_REDIS_PORT] = $config[SessionConstants::YVES_SESSION_REDIS_PORT];
$config[SessionConstants::ZED_SESSION_REDIS_PASSWORD] = $config[SessionConstants::YVES_SESSION_REDIS_PASSWORD];
$config[SessionConstants::ZED_SESSION_REDIS_DATABASE] = 2;
$config[SessionConstants::ZED_SESSION_TIME_TO_LIVE] = SessionConstants::SESSION_LIFETIME_0_5_HOUR;

// ---------- Jenkins
$config[SetupConstants::JENKINS_BASE_URL] = 'http://jenkins-marketplace:8080/';
$config[SetupConstants::JENKINS_DIRECTORY] = '/data/shop/production/shared/data/common/jenkins';

// ---------- Zed request
$config[ZedRequestConstants::TRANSFER_DEBUG_SESSION_FORWARD_ENABLED] = false;
$config[ZedRequestConstants::SET_REPEAT_DATA] = true;
$config[ZedRequestConstants::YVES_REQUEST_REPEAT_DATA_PATH] = APPLICATION_ROOT_DIR . '/data/' . Store::getInstance()->getStoreName() . '/' . APPLICATION_ENV . '/yves-requests';

// ---------- Navigation
$config[ZedNavigationConstants::ZED_NAVIGATION_CACHE_ENABLED] = true;

// ---------- Error handling
$config[ErrorHandlerConstants::DISPLAY_ERRORS] = true;
$config[ErrorHandlerConstants::ERROR_RENDERER] = WebExceptionErrorRenderer::class;

// ---------- ACL
$config[AclConstants::ACL_USER_RULE_WHITELIST][] = [
    'bundle' => 'wdt',
    'controller' => '*',
    'action' => '*',
    'type' => 'allow',
];

// ---------- Auto-loader
$config[KernelConstants::AUTO_LOADER_UNRESOLVABLE_CACHE_ENABLED] = false;

// ---------- Logging
$config[LogConstants::LOG_LEVEL] = Logger::WARNING;

$baseLogFilePath = sprintf('%s/data/%s/logs', APPLICATION_ROOT_DIR, $CURRENT_STORE);

$config[LogConstants::EXCEPTION_LOG_FILE_PATH_YVES] = $baseLogFilePath . '/YVES/exception.log';
$config[LogConstants::EXCEPTION_LOG_FILE_PATH_ZED] = $baseLogFilePath . '/ZED/exception.log';

$config[LogConstants::LOG_MAIL_RECIPIENTS] = [
    'developer@durst.shop' => 'Developers'
];

// ---------- Events
$config[EventConstants::LOGGER_ACTIVE] = true;

// ---------- Auto-loader
$config[KernelConstants::AUTO_LOADER_UNRESOLVABLE_CACHE_ENABLED] = true;

// ---------- Twig
$config[TwigConstants::YVES_PATH_CACHE_ENABLED] = true;
$config[TwigConstants::ZED_PATH_CACHE_ENABLED] = true;

// ---------- App Rest API
$config[AppRestApiConstants::UPLOAD_BRANCH_FOLDER_HOST] = 'https://haendler.durst.shop';

// ---------- Heidelpay REST
$config[HeidelpayRestConstants::HEIDELPAY_REST_IS_DEBUG] = false;

// ---------- Tour
$config[TourConstants::EDIFACT_TESTRUN] = false;

// ---------- Google API
$config[GoogleApiConstants::GOOGLE_API_GEOCODING_KEY] = 'AIzaSyDQ5HqpgxEWM2ssyDs2eCVx4G0blNQGrYs';

// ---------- Invoice
$config[InvoiceConfig::INVOICE_REFERENCE_PREFIX] = 'R';

// ---------- Realax Invoice License Keys
$config[AccountingConstants::REALAX_RECIPIENTS] = [
    'oliver.gail@durst.de' => 'Oliver Gail',
    'buchhaltung@durst.de' => 'Durst Buchhaltung'
];

// ---------- State machine (OMS)
$config[OmsConstants::OMS_ERROR_MAIL_RECIPIENTS] = [
    'support@durst.de' => 'Durst Support',
    'service@durst.shop' => 'Durst Service',
];

// ---------- Product export (e.g. GBZ)
$config[ProductExportConstants::BATCH_SIZE] = 100;

// ---------- Mail
$config[MailConstants::MAIL_MERCHANT_CENTER_BASE_URL] = 'https://haendler.durst.shop';
$config[MailConstants::MAIL_CANCEL_ORDER_BASE_URL] = 'https://customer.durst.shop/cancel-order/cancel?t=%s';
