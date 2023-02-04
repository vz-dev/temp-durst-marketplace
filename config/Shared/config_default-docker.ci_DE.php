<?php

use Spryker\Shared\Propel\PropelConstants;

// ############################################################################
// ######################### DOCKER CI CONFIGURATION ##########################
// ############################################################################

require 'config_default-docker.devtest_DE.php';

// >>> DATABASE

$config[PropelConstants::ZED_DB_DATABASE] = getenv('SPRYKER_DB_DATABASE') ?: 'eu-docker-ci';
