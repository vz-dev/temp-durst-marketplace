<?php
/**
 * Durst - project - ManufacturerEntityNotFoundException.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 11.06.18
 * Time: 16:54
 */

namespace Pyz\Zed\AkeneoPimMiddlewareConnector\Exception;


class ManufacturerEntityNotFoundException extends \Exception
{
    const MESSAGE = 'The manufacturer with the code %s could not be found';

}