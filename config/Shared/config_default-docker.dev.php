<?php

use Monolog\Logger;
use Pyz\Shared\Customer\CustomerConstants;
use Pyz\Shared\Log\LogConstants;
use Pyz\Shared\Mail\MailConstants;
use Pyz\Shared\Newsletter\NewsletterConstants;
use Pyz\Shared\Search\SearchConstants;
use Pyz\Shared\Setup\SetupConstants;
use Pyz\Shared\WebProfiler\WebProfilerConstants;
use Spryker\Shared\Application\ApplicationConstants;
// use Spryker\Shared\DocumentationGeneratorRestApi\DocumentationGeneratorRestApiConstants;
use Spryker\Shared\ErrorHandler\ErrorHandlerConstants;
use Spryker\Shared\ErrorHandler\ErrorRenderer\WebExceptionErrorRenderer;
use Spryker\Shared\ErrorHandler\ErrorRenderer\WebHtmlErrorRenderer;
use Spryker\Shared\Event\EventConstants;
use Spryker\Shared\GlueApplication\GlueApplicationConstants;
// use Spryker\Shared\Kernel\KernelConstants;
use Spryker\Shared\Oauth\OauthConstants;
use Spryker\Shared\ProductManagement\ProductManagementConstants;
use Spryker\Shared\Propel\PropelConstants;
use Spryker\Shared\PropelOrm\PropelOrmConstants;
use Spryker\Shared\PropelQueryBuilder\PropelQueryBuilderConstants;
use Spryker\Shared\Queue\QueueConfig;
use Spryker\Shared\Queue\QueueConstants;
use Spryker\Shared\RabbitMq\RabbitMqConstants;
// use Spryker\Shared\Router\RouterConstants;
use Spryker\Shared\Session\SessionConstants;
use Spryker\Shared\Storage\StorageConstants;
// use Spryker\Shared\Testify\TestifyConstants;
use Spryker\Shared\ZedRequest\ZedRequestConstants;
use Spryker\Zed\Propel\PropelConfig;
use SprykerEco\Shared\AkeneoPim\AkeneoPimConstants;
// use SprykerShop\Shared\CalculationPage\CalculationPageConstants;
// use SprykerShop\Shared\ErrorPage\ErrorPageConstants;
// use SprykerShop\Shared\ShopApplication\ShopApplicationConstants;
// use SprykerShop\Shared\WebProfilerWidget\WebProfilerWidgetConstants;

// ############################################################################
// ##################### DOCKER DEVELOPMENT CONFIGURATION #####################
// ############################################################################

require 'config_default-development.php';

// ----------------------------------------------------------------------------
// ------------------------------ CODEBASE ------------------------------------
// ----------------------------------------------------------------------------

// >>> Debug

$config[ApplicationConstants::ENABLE_APPLICATION_DEBUG]
    // = $config[ShopApplicationConstants::ENABLE_APPLICATION_DEBUG]
    = (bool)getenv('SPRYKER_DEBUG_ENABLED');

$config[PropelConstants::PROPEL_DEBUG] = (bool)getenv('SPRYKER_DEBUG_PROPEL_ENABLED');
// $config[CalculationPageConstants::ENABLE_CART_DEBUG] = (bool)getenv('SPRYKER_DEBUG_ENABLED');
// $config[ErrorPageConstants::ENABLE_ERROR_404_STACK_TRACE] = (bool)getenv('SPRYKER_DEBUG_ENABLED');
$config[GlueApplicationConstants::GLUE_APPLICATION_REST_DEBUG] = (bool)getenv('SPRYKER_DEBUG_ENABLED');

// >>> Dev tools

if (interface_exists(WebProfilerConstants::class, true)) {
    $config[WebProfilerConstants::ENABLE_WEB_PROFILER]
        // = $config[WebProfilerWidgetConstants::IS_WEB_PROFILER_ENABLED]
        = false;
}
// $config[KernelConstants::ENABLE_CONTAINER_OVERRIDING] = (bool)getenv('SPRYKER_TESTING_ENABLED');
// $config[DocumentationGeneratorRestApiConstants::ENABLE_REST_API_DOCUMENTATION_GENERATION] = true;

// >>> Error handler

$config[ErrorHandlerConstants::DISPLAY_ERRORS] = true;
$config[ErrorHandlerConstants::ERROR_RENDERER] = getenv('SPRYKER_DEBUG_ENABLED') ? WebExceptionErrorRenderer::class : WebHtmlErrorRenderer::class;
// $config[ErrorHandlerConstants::IS_PRETTY_ERROR_HANDLER_ENABLED] = (bool)getenv('SPRYKER_DEBUG_ENABLED');
$config[ErrorHandlerConstants::ERROR_LEVEL] = getenv('SPRYKER_DEBUG_DEPRECATIONS_ENABLED') ? E_ALL : $config[ErrorHandlerConstants::ERROR_LEVEL];

// ----------------------------------------------------------------------------
// ------------------------------ SERVICES ------------------------------------
// ----------------------------------------------------------------------------

// >>> DATABASE

$config[PropelConstants::ZED_DB_ENGINE]
    = $config[PropelQueryBuilderConstants::ZED_DB_ENGINE]
    = strtolower(getenv('SPRYKER_DB_ENGINE') ?: '') ?: PropelConfig::DB_ENGINE_PGSQL;
$config[PropelConstants::ZED_DB_HOST] = getenv('SPRYKER_DB_HOST');
$config[PropelConstants::ZED_DB_PORT] = getenv('SPRYKER_DB_PORT');
$config[PropelConstants::USE_SUDO_TO_MANAGE_DATABASE] = false;

// >>> SEARCH

$config[SearchConstants::ELASTICA_PARAMETER__HOST] = getenv('SPRYKER_SEARCH_HOST');
$config[SearchConstants::ELASTICA_PARAMETER__TRANSPORT] = getenv('SPRYKER_SEARCH_PROTOCOL') ?: 'http';
$config[SearchConstants::ELASTICA_PARAMETER__PORT] = getenv('SPRYKER_SEARCH_PORT');
$config[SearchConstants::ELASTICA_PARAMETER__AUTH_HEADER] = getenv('SPRYKER_SEARCH_BASIC_AUTH') ?: null;

// >>> STORAGE

$config[StorageConstants::STORAGE_KV_SOURCE] = strtolower(getenv('SPRYKER_KEY_VALUE_STORE_ENGINE')) ?: 'redis';
$config[StorageConstants::STORAGE_REDIS_PROTOCOL] = getenv('SPRYKER_KEY_VALUE_STORE_PROTOCOL') ?: 'tcp';
$config[StorageConstants::STORAGE_REDIS_HOST] = getenv('SPRYKER_KEY_VALUE_STORE_HOST');
$config[StorageConstants::STORAGE_REDIS_PORT] = getenv('SPRYKER_KEY_VALUE_STORE_PORT');
$config[StorageConstants::STORAGE_REDIS_PASSWORD] = getenv('SPRYKER_KEY_VALUE_STORE_PASSWORD');
$config[StorageConstants::STORAGE_REDIS_DATABASE] = getenv('SPRYKER_KEY_VALUE_STORE_NAMESPACE') ?: 1;

// >>> SESSION FRONTEND

$config[SessionConstants::YVES_SESSION_REDIS_PROTOCOL] = getenv('SPRYKER_SESSION_FE_PROTOCOL') ?: 'tcp';
$config[SessionConstants::YVES_SESSION_REDIS_HOST] = getenv('SPRYKER_SESSION_FE_HOST') ?: 'session';
$config[SessionConstants::YVES_SESSION_REDIS_PORT] = getenv('SPRYKER_SESSION_FE_PORT') ?: 6379;
$config[SessionConstants::YVES_SESSION_REDIS_PASSWORD] = getenv('SPRYKER_SESSION_FE_PASSWORD');
$config[SessionConstants::YVES_SESSION_REDIS_DATABASE] = getenv('SPRYKER_SESSION_FE_NAMESPACE') ?: 1;

// >>> SESSION BACKOFFICE

$config[SessionConstants::ZED_SESSION_REDIS_PROTOCOL] = getenv('SPRYKER_SESSION_BE_PROTOCOL') ?: 'tcp';
$config[SessionConstants::ZED_SESSION_REDIS_HOST] = getenv('SPRYKER_SESSION_BE_HOST') ?: 'session';
$config[SessionConstants::ZED_SESSION_REDIS_PORT] = getenv('SPRYKER_SESSION_BE_PORT') ?: 6379;
$config[SessionConstants::ZED_SESSION_REDIS_PASSWORD] = getenv('SPRYKER_SESSION_BE_PASSWORD');
$config[SessionConstants::ZED_SESSION_REDIS_DATABASE] = getenv('SPRYKER_SESSION_BE_NAMESPACE') ?: 3;

// >>> QUEUE

$config[QueueConstants::QUEUE_ADAPTER_CONFIGURATION][EventConstants::EVENT_QUEUE][QueueConfig::CONFIG_MAX_WORKER_NUMBER] = 1;

$config[RabbitMqConstants::RABBITMQ_API_HOST] = getenv('SPRYKER_BROKER_API_HOST');
$config[RabbitMqConstants::RABBITMQ_API_PORT] = getenv('SPRYKER_BROKER_API_PORT');
$config[RabbitMqConstants::RABBITMQ_API_USERNAME] = getenv('SPRYKER_BROKER_API_USERNAME');
$config[RabbitMqConstants::RABBITMQ_API_PASSWORD] = getenv('SPRYKER_BROKER_API_PASSWORD');

$config[RabbitMqConstants::RABBITMQ_HOST] = getenv('SPRYKER_BROKER_HOST');
$config[RabbitMqConstants::RABBITMQ_PORT] = getenv('SPRYKER_BROKER_PORT');
$config[RabbitMqConstants::RABBITMQ_PASSWORD] = getenv('SPRYKER_BROKER_PASSWORD');

// >>> LOGGER

$config[EventConstants::LOGGER_ACTIVE] = true;
$config[PropelOrmConstants::PROPEL_SHOW_EXTENDED_EXCEPTION] = true;
$config[LogConstants::LOG_LEVEL] = getenv('SPRYKER_DEBUG_ENABLED') ? Logger::INFO : Logger::DEBUG;

// >>> SCHEDULER

$config[SetupConstants::JENKINS_BASE_URL] = sprintf(
    '%s://%s:%s/',
    getenv('SPRYKER_SCHEDULER_PROTOCOL') ?: 'http',
    getenv('SPRYKER_SCHEDULER_HOST'),
    getenv('SPRYKER_SCHEDULER_PORT')
);

// >>> MAIL

$config[MailConstants::MAIL_SMTP_HOST] = getenv('SPRYKER_SMTP_HOST') ?: 'mail_catcher';
$config[MailConstants::MAIL_SMTP_PORT] = getenv('SPRYKER_SMTP_PORT') ?: 1025;

// >>> ZED REQUEST

$config[ZedRequestConstants::TRANSFER_DEBUG_SESSION_FORWARD_ENABLED] = (bool)getenv('SPRYKER_DEBUG_ENABLED');
$config[ZedRequestConstants::SET_REPEAT_DATA] = (bool)getenv('SPRYKER_DEBUG_ENABLED');

if (!getenv('SPRYKER_SSL_ENABLE')) {
// ----------------------------------------------------------------------------
// ------------------------------ SECURITY ------------------------------------
// ----------------------------------------------------------------------------

    $config[SessionConstants::ZED_SSL_ENABLED]
        = $config[SessionConstants::YVES_SSL_ENABLED]
        // = $config[RouterConstants::YVES_IS_SSL_ENABLED]
        // = $config[RouterConstants::ZED_IS_SSL_ENABLED]
        = $config[ApplicationConstants::ZED_SSL_ENABLED]
        = $config[ApplicationConstants::YVES_SSL_ENABLED]
        = false;

// ----------------------------------------------------------------------------
// ------------------------------ BACKOFFICE ----------------------------------
// ----------------------------------------------------------------------------

    $backofficePort = (int)(getenv('SPRYKER_BE_PORT')) ?: 80;
    $config[ApplicationConstants::BASE_URL_ZED] = sprintf(
        'http://%s%s',
        getenv('SPRYKER_BE_HOST'),
        $backofficePort !== 80 ? ':' . $backofficePort : ''
    );

// ----------------------------------------------------------------------------
// ------------------------------ FRONTEND ------------------------------------
// ----------------------------------------------------------------------------

    $yvesHost = getenv('SPRYKER_FE_HOST');
    $yvesPort = (int)(getenv('SPRYKER_FE_PORT')) ?: 80;
    $config[ApplicationConstants::BASE_URL_YVES]
        = $config[CustomerConstants::BASE_URL_YVES]
        = $config[ProductManagementConstants::BASE_URL_YVES]
        = $config[NewsletterConstants::BASE_URL_YVES]
        = sprintf(
            'http://%s%s',
            $yvesHost,
            $yvesPort !== 80 ? ':' . $yvesPort : ''
        );

// ----------------------------------------------------------------------------
// ------------------------------ API -----------------------------------------
// ----------------------------------------------------------------------------

    $glueHost = getenv('SPRYKER_API_HOST') ?: 'localhost';
    $gluePort = (int)(getenv('SPRYKER_API_PORT')) ?: 80;

    $config[GlueApplicationConstants::GLUE_APPLICATION_DOMAIN] = sprintf(
        'http://%s%s',
        $glueHost,
        $gluePort !== 80 ? ':' . $gluePort : ''
    );

    // if (class_exists(TestifyConstants::class, true)) {
    //     $config[TestifyConstants::GLUE_APPLICATION_DOMAIN] = $config[GlueApplicationConstants::GLUE_APPLICATION_DOMAIN];
    // }
}

// ----------------------------------------------------------------------------
// ------------------------------ OMS -----------------------------------------
// ----------------------------------------------------------------------------

// require 'common/config_oms-development.php';

// ----------------------------------------------------------------------------
// ------------------------------ AUTHENTICATION ------------------------------
// ----------------------------------------------------------------------------

// >>> OAUTH

$config[OauthConstants::PRIVATE_KEY_PATH] = str_replace(
    '__LINE__',
    PHP_EOL,
    getenv('SPRYKER_OAUTH_KEY_PRIVATE') ?: ''
) ?: null;
$config[OauthConstants::PUBLIC_KEY_PATH]
    // = $config[OauthCryptographyConstants::PUBLIC_KEY_PATH]
    = str_replace(
    '__LINE__',
    PHP_EOL,
    getenv('SPRYKER_OAUTH_KEY_PUBLIC') ?: ''
) ?: null;
$config[OauthConstants::ENCRYPTION_KEY] = getenv('SPRYKER_OAUTH_ENCRYPTION_KEY') ?: null;
$config[OauthConstants::OAUTH_CLIENT_IDENTIFIER] = getenv('SPRYKER_OAUTH_CLIENT_IDENTIFIER') ?: null;
$config[OauthConstants::OAUTH_CLIENT_SECRET] = getenv('SPRYKER_OAUTH_CLIENT_SECRET') ?: null;
