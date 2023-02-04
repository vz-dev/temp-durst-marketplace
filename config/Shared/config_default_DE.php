<?php

use Pyz\Shared\Collector\CollectorConstants;
use Pyz\Shared\Newsletter\NewsletterConstants;
use Pyz\Shared\Search\SearchConstants;
use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Shared\Customer\CustomerConstants;
use Spryker\Shared\Event\EventConstants;
use Spryker\Shared\GlueApplication\GlueApplicationConstants;
use Spryker\Shared\Payolution\PayolutionConstants;
use Spryker\Shared\ProductManagement\ProductManagementConstants;
use Spryker\Shared\Session\SessionConstants;
use Spryker\Shared\ZedRequest\ZedRequestConstants;
use SprykerEco\Shared\Heidelpay\HeidelpayConstants;

// ---------- Yves host
$config[ApplicationConstants::HOST_YVES] = 'www.de.durst.local';
$config[ApplicationConstants::PORT_YVES] = '';
$config[ApplicationConstants::PORT_SSL_YVES] = '';
$config[ApplicationConstants::BASE_URL_YVES] = sprintf(
    'http://%s%s',
    $config[ApplicationConstants::HOST_YVES],
    $config[ApplicationConstants::PORT_YVES]
);
$config[ApplicationConstants::BASE_URL_SSL_YVES] = sprintf(
    'https://%s%s',
    $config[ApplicationConstants::HOST_YVES],
    $config[ApplicationConstants::PORT_SSL_YVES]
);
$config[ProductManagementConstants::BASE_URL_YVES] = $config[ApplicationConstants::BASE_URL_YVES];
$config[PayolutionConstants::BASE_URL_YVES] = $config[ApplicationConstants::BASE_URL_YVES];
$config[NewsletterConstants::BASE_URL_YVES] = $config[ApplicationConstants::BASE_URL_YVES];
$config[CustomerConstants::BASE_URL_YVES] = $config[ApplicationConstants::BASE_URL_YVES];

// ---------- Zed host
$config[ApplicationConstants::HOST_ZED] = 'zed.de.durst.local';
$config[ApplicationConstants::PORT_ZED] = '';
$config[ApplicationConstants::PORT_SSL_ZED] = '';
$config[ApplicationConstants::BASE_URL_ZED] = sprintf(
    'http://%s%s',
    $config[ApplicationConstants::HOST_ZED],
    $config[ApplicationConstants::PORT_ZED]
);
$config[ApplicationConstants::BASE_URL_SSL_ZED] = sprintf(
    'https://%s%s',
    $config[ApplicationConstants::HOST_ZED],
    $config[ApplicationConstants::PORT_SSL_ZED]
);
$config[ZedRequestConstants::HOST_ZED_API] = $config[ApplicationConstants::HOST_ZED];
$config[ZedRequestConstants::BASE_URL_ZED_API] = $config[ApplicationConstants::BASE_URL_ZED];
$config[ZedRequestConstants::BASE_URL_SSL_ZED_API] = $config[ApplicationConstants::BASE_URL_SSL_ZED];

// ---------- Assets / Media
$config[ApplicationConstants::BASE_URL_STATIC_ASSETS] = $config[ApplicationConstants::BASE_URL_YVES];
$config[ApplicationConstants::BASE_URL_STATIC_MEDIA] = $config[ApplicationConstants::BASE_URL_YVES];
$config[ApplicationConstants::BASE_URL_SSL_STATIC_ASSETS] = $config[ApplicationConstants::BASE_URL_SSL_YVES];
$config[ApplicationConstants::BASE_URL_SSL_STATIC_MEDIA] = $config[ApplicationConstants::BASE_URL_SSL_YVES];

// ---------- Session
$config[SessionConstants::YVES_SESSION_COOKIE_NAME] = $config[ApplicationConstants::HOST_YVES];
$config[SessionConstants::YVES_SESSION_COOKIE_DOMAIN] = $config[ApplicationConstants::HOST_YVES];
$config[SessionConstants::ZED_SESSION_COOKIE_NAME] = $config[ApplicationConstants::HOST_ZED];

// ---------- Events
$config[EventConstants::LOG_FILE_PATH] = APPLICATION_ROOT_DIR . '/data/DE/logs/application_events.log';

// ---------- Elasticsearch
$ELASTICA_INDEX_NAME = 'de_search';
$config[SearchConstants::ELASTICA_PARAMETER__INDEX_NAME] = $ELASTICA_INDEX_NAME;
$config[CollectorConstants::ELASTICA_PARAMETER__INDEX_NAME] = $ELASTICA_INDEX_NAME;
$ELASTICA_TIME_SLOT_INDEX_NAME = 'de_time_slot';
$config[CollectorConstants::ELASTICA_PARAMETER__TIME_SLOT_INDEX_NAME] = $ELASTICA_TIME_SLOT_INDEX_NAME;
$config[SearchConstants::ELASTICA_PARAMETER__TIME_SLOT_INDEX_NAME] = $ELASTICA_TIME_SLOT_INDEX_NAME;

// ----------- Elasticsearch - Graphmaster Timeslot Index Settings
$ELASTICA_GRAPHMASTERS_TIME_SLOT_INDEX_NAME = 'de_gm_time_slot';
$config[CollectorConstants::ELASTICA_PARAMETER__GM_TIME_SLOT_INDEX_NAME] = $ELASTICA_GRAPHMASTERS_TIME_SLOT_INDEX_NAME;
$config[SearchConstants::ELASTICA_PARAMETER__GM_TIME_SLOT_INDEX_NAME] = $ELASTICA_GRAPHMASTERS_TIME_SLOT_INDEX_NAME;

// Shop configuration values
$config[HeidelpayConstants::CONFIG_HEIDELPAY_SANDBOX_REQUEST] = true;

$config[HeidelpayConstants::CONFIG_HEIDELPAY_LANGUAGE_CODE] = 'DE';
$config[HeidelpayConstants::CONFIG_HEIDELPAY_PAYMENT_RESPONSE_URL] = 'https://' . $config[ApplicationConstants::HOST_YVES] . '/heidelpay/payment';
$config[HeidelpayConstants::CONFIG_YVES_CHECKOUT_SUCCESS_URL] = 'http://' . $config[ApplicationConstants::HOST_YVES] . '/checkout/success';
$config[HeidelpayConstants::CONFIG_YVES_URL] = 'http://' . $config[ApplicationConstants::HOST_YVES];
$config[HeidelpayConstants::CONFIG_YVES_CHECKOUT_PAYMENT_FAILED_URL] = 'http://' . $config[ApplicationConstants::HOST_YVES] . '/heidelpay/payment-failed?error_code=%s';
$config[HeidelpayConstants::CONFIG_YVES_CHECKOUT_IDEAL_AUTHORIZE_URL] = 'http://' . $config[ApplicationConstants::HOST_YVES] . '/heidelpay/ideal-authorize';
$config[HeidelpayConstants::CONFIG_YVES_CHECKOUT_PAYMENT_STEP_PATH] = '/checkout/payment';
$config[HeidelpayConstants::CONFIG_YVES_CHECKOUT_SUMMARY_STEP_URL] = 'http://' . $config[ApplicationConstants::HOST_YVES] . '/checkout/summary';
$config[HeidelpayConstants::CONFIG_YVES_CHECKOUT_ASYNC_RESPONSE_URL] = 'http://' . $config[ApplicationConstants::HOST_YVES] . '/heidelpay/cc-register-response';
$config[HeidelpayConstants::CONFIG_YVES_CHECKOUT_REGISTRATION_SUCCESS_URL] = 'http://' . $config[ApplicationConstants::HOST_YVES] . '/heidelpay/cc-register-success?id_registration=%s';
$config[HeidelpayConstants::CONFIG_YVES_CHECKOUT_PAYMENT_FRAME_CUSTOM_CSS_URL] = '';
$config[HeidelpayConstants::CONFIG_YVES_CHECKOUT_PAYMENT_FRAME_PREVENT_ASYNC_REDIRECT] = "FALSE";

$config[GlueApplicationConstants::GLUE_APPLICATION_DOMAIN] = 'http://glue.de.durst.local';
