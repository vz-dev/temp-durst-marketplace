<?php
/**
 * Durst - project - DriverDownloadLatestReleaseRequestInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-08-12
 * Time: 14:25
 */

namespace Pyz\Yves\AppRestApi\Handler\Json\Request;


use Pyz\Yves\AppRestApi\Handler\Json\Response\DriverLoginResponseInterface;

interface DriverDownloadLatestReleaseRequestInterface
{
    public const KEY_TOKEN = DriverLoginResponseInterface::KEY_TOKEN;
}