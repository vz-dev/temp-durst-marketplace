<?php
/**
 * Durst - project - DriverAppConfig.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-08-06
 * Time: 13:27
 */

namespace Pyz\Shared\DriverApp;


interface DriverAppConfig
{
    /**
     * Path where uploaded apk files will be stored
     */
    public const DRIVER_APP_UPLOAD_PATH = 'DRIVER_APP_UPLOAD_PATH';
}