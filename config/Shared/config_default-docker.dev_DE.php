<?php

use Pyz\Shared\Mail\MailConstants;
use Spryker\Shared\Propel\PropelConstants;
use Spryker\Shared\RabbitMq\RabbitMqConstants;

// ############################################################################
// ##################### DOCKER DEVELOPMENT CONFIGURATION #####################
// ############################################################################

require 'config_default-development_DE.php';

// ----------------------------------------------------------------------------
// ------------------------------ SERVICES ------------------------------------
// ----------------------------------------------------------------------------

// >>> DATABASE

// $config[PropelConstants::ZED_DB_USERNAME] = getenv('SPRYKER_DB_USERNAME');
$config[PropelConstants::ZED_DB_USERNAME] = 'root';
$config[PropelConstants::ZED_DB_PASSWORD] = getenv('SPRYKER_DB_PASSWORD');
$config[PropelConstants::ZED_DB_DATABASE] = getenv('SPRYKER_DB_DATABASE') ?: 'eu-docker-dev';

// >>> MAIL
$config[MailConstants::MAILCATCHER_GUI] = 'http://mail.durst.local';

// >>> QUEUE

$config[RabbitMqConstants::RABBITMQ_USERNAME] = getenv('SPRYKER_BROKER_USERNAME') ?: 'spryker';
$config[RabbitMqConstants::RABBITMQ_VIRTUAL_HOST] = getenv('SPRYKER_BROKER_NAMESPACE') ?: 'de_search';
