<?php

/**
 * This is the global runtime configuration for Yves and Generated_Yves_Zed in a production environment.
 */

use Monolog\Logger;
use Pyz\Shared\AppRestApi\AppRestApiConstants;
use Pyz\Shared\Campaign\CampaignConstants;
use Pyz\Shared\Mail\MailConstants;
use Pyz\Shared\Tax\TaxConstants;
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
use Spryker\Shared\Log\LogConstants;
use Spryker\Shared\Propel\PropelConstants;
use Spryker\Shared\PropelOrm\PropelOrmConstants;
use Spryker\Shared\PropelQueryBuilder\PropelQueryBuilderConstants;
use Spryker\Shared\RabbitMq\RabbitMqConstants;
use Spryker\Shared\Search\SearchConstants;
use Spryker\Shared\Session\SessionConstants;
use Spryker\Shared\Setup\SetupConstants;
use Spryker\Shared\Storage\StorageConstants;
use Spryker\Shared\ZedNavigation\ZedNavigationConstants;
use Spryker\Shared\ZedRequest\ZedRequestConstants;
use SprykerEco\Shared\Heidelpay\HeidelpayConstants;

$CURRENT_STORE = Store::getInstance()->getStoreName();

/// ----
///
///
///
// ---------- General environment
$config[KernelConstants::SPRYKER_ROOT] = APPLICATION_ROOT_DIR . '/vendor/spryker';
$config[KernelConstants::STORE_PREFIX] = 'DEV';
$config[ApplicationConstants::ENABLE_APPLICATION_DEBUG] = true;
$config[WebProfilerConstants::ENABLE_WEB_PROFILER]
    = $config[ConfigConstants::ENABLE_WEB_PROFILER]
    = true;

$config[ApplicationConstants::ZED_SSL_ENABLED] = true;
$config[ApplicationConstants::YVES_SSL_ENABLED] = true;

// ---------- Propel
$config[PropelConstants::PROPEL_DEBUG] = true;
$config[PropelOrmConstants::PROPEL_SHOW_EXTENDED_EXCEPTION] = true;
$config[PropelConstants::ZED_DB_USERNAME] = 'spryker';
$config[PropelConstants::ZED_DB_PASSWORD] = 'mate20mg';
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
$config[SessionConstants::YVES_SESSION_COOKIE_SECURE] = false;
$config[SessionConstants::YVES_SESSION_REDIS_PROTOCOL] = $config[StorageConstants::STORAGE_REDIS_PROTOCOL];
$config[SessionConstants::YVES_SESSION_REDIS_HOST] = $config[StorageConstants::STORAGE_REDIS_HOST];
$config[SessionConstants::YVES_SESSION_REDIS_PORT] = $config[StorageConstants::STORAGE_REDIS_PORT];
$config[SessionConstants::YVES_SESSION_REDIS_PASSWORD] = $config[StorageConstants::STORAGE_REDIS_PASSWORD];
$config[SessionConstants::YVES_SESSION_REDIS_DATABASE] = 1;
$config[SessionConstants::ZED_SESSION_COOKIE_SECURE] = false;
$config[SessionConstants::ZED_SESSION_REDIS_PROTOCOL] = $config[SessionConstants::YVES_SESSION_REDIS_PROTOCOL];
$config[SessionConstants::ZED_SESSION_REDIS_HOST] = $config[SessionConstants::YVES_SESSION_REDIS_HOST];
$config[SessionConstants::ZED_SESSION_REDIS_PORT] = $config[SessionConstants::YVES_SESSION_REDIS_PORT];
$config[SessionConstants::ZED_SESSION_REDIS_PASSWORD] = $config[SessionConstants::YVES_SESSION_REDIS_PASSWORD];
$config[SessionConstants::ZED_SESSION_REDIS_DATABASE] = 2;
$config[SessionConstants::ZED_SESSION_TIME_TO_LIVE] = SessionConstants::SESSION_LIFETIME_1_YEAR;

// ---------- Jenkins
$config[SetupConstants::JENKINS_BASE_URL] = 'http://jenkins-marketplace:8080/';
$config[SetupConstants::JENKINS_DIRECTORY] = '/data/shop/staging/shared/data/common/jenkins';

// ---------- Zed request
$config[ZedRequestConstants::TRANSFER_DEBUG_SESSION_FORWARD_ENABLED] = true;
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
$config[LogConstants::LOG_LEVEL] = Logger::INFO;

$baseLogFilePath = sprintf('%s/data/%s/logs', APPLICATION_ROOT_DIR, $CURRENT_STORE);

$config[LogConstants::EXCEPTION_LOG_FILE_PATH_YVES] = $baseLogFilePath . '/YVES/exception.log';
$config[LogConstants::EXCEPTION_LOG_FILE_PATH_ZED] = $baseLogFilePath . '/ZED/exception.log';

// ---------- Events
$config[EventConstants::LOGGER_ACTIVE] = true;

// media
$config[AppRestApiConstants::UPLOAD_BRANCH_FOLDER_HOST] = 'https://haendler.stage02.durst.shop';

// Heidelpay
$config[HeidelpayConstants::CONFIG_HEIDELPAY_SANDBOX_REQUEST] = true;

// Tour Export: PHP path to executable
$config[TourConstants::PHP_PATH_FOR_CONSOLE] = '/usr/local/bin/php';
// Show Tours 3 days in advance for debugging in the Driver-App
$config[TourConstants::DRIVER_APP_TOUR_FUTURE_CUTOFF] = '+3 days midnight';

// Tax
$config[TaxConstants::TAX_CORONA_DEADLINE] = new DateTime('2020-06-27');

// ---------- Mail
$config[MailConstants::MAIL_MERCHANT_CENTER_BASE_URL] = 'https://haendler.stage02.durst.shop';
$config[MailConstants::MAIL_CANCEL_ORDER_BASE_URL] = 'https://customer.stage02.durst.shop/cancel-order/cancel?t=%s';

// ---------- Campaign setting, copied from AppRestApi
$config[CampaignConstants::DEEP_LINK_URL] = 'https://www.webshop-stage.durst.shop/start?branchCode=%s&productSku=%s';
