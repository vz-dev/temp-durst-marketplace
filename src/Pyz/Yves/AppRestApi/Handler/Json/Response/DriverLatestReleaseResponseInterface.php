<?php
/**
 * Durst - project - DriverLatestReleaseResponseInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-08-12
 * Time: 11:42
 */

namespace Pyz\Yves\AppRestApi\Handler\Json\Response;


interface DriverLatestReleaseResponseInterface
{
    public const KEY_AUTH_VALID = DriverLoginResponseInterface::KEY_AUTH_VALID;
    public const KEY_IS_UPDATABLE = DriverLoginResponseInterface::KEY_IS_UPDATABLE;
    public const KEY_VERSION = 'version';
}
