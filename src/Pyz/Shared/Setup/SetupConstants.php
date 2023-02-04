<?php
/**
 * Durst - project - SetupConstants.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 06.12.18
 * Time: 09:19
 */

namespace Pyz\Shared\Setup;

use Spryker\Shared\Setup\SetupConstants as SprykerSetupConstants;

interface SetupConstants extends SprykerSetupConstants
{
    public const DOCKER_SCRIPT_PATH = 'DOCKER_SCRIPT_PATH';
    public const PHP_BINARY_PATH = 'PHP_BINARY_PATH';
}