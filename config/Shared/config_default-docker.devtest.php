<?php

use Monolog\Logger;
use Pyz\Shared\Log\LogConstants;
use Spryker\Shared\Event\EventConstants;
use Spryker\Shared\Queue\QueueConstants;

// ############################################################################
// ################ DOCKER DEVELOPMENT TESTING CONFIGURATION ##################
// ############################################################################

require 'config_default-devtest.php';
require 'config_default-docker.dev.php';

// ----------------------------------------------------------------------------
// ------------------------------ SERVICES ------------------------------------
// ----------------------------------------------------------------------------

// >>> LOGGING

$config[LogConstants::LOG_LEVEL] = Logger::ERROR;
$config[EventConstants::LOG_FILE_PATH]
    // = $config[PropelConstants::LOG_FILE_PATH]
    = $config[LogConstants::LOG_FILE_PATH_YVES]
    = $config[LogConstants::LOG_FILE_PATH_ZED]
    // = $config[LogConstants::LOG_FILE_PATH_GLUE]
    = $config[LogConstants::LOG_FILE_PATH]
    = $config[QueueConstants::QUEUE_WORKER_OUTPUT_FILE_NAME]
    = getenv('SPRYKER_LOG_STDOUT') ?: '/dev/null';
