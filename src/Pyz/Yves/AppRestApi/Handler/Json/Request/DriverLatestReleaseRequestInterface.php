<?php
/**
 * Durst - project - DriverLatestReleaseRequestInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-08-12
 * Time: 11:42
 */

namespace Pyz\Yves\AppRestApi\Handler\Json\Request;

use Pyz\Yves\AppRestApi\Handler\Json\Response\DriverLoginResponseInterface;

interface DriverLatestReleaseRequestInterface
{
    public const KEY_TOKEN = DriverLoginResponseInterface::KEY_TOKEN;
}
