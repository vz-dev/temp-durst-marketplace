<?php

use Spryker\Shared\Propel\PropelConstants;

// ############################################################################
// ################ DOCKER DEVELOPMENT TESTING CONFIGURATION ##################
// ############################################################################

require 'config_default-devtest_DE.php';
require 'config_default-docker.dev_DE.php';

// >>> DATABASE

$config[PropelConstants::ZED_DB_DATABASE] = getenv('SPRYKER_DB_DATABASE').'test' ?: 'eu-docker-devtest';
