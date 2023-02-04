<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 16.10.17
 * Time: 15:52
 */

namespace Pyz\Zed\DeliveryArea\Business\Exception;


class DeliveryAreaNotFoundException extends \Exception
{
    const NOT_FOUND = 'The delivery area with the id %d could not be found';
    const NOT_FOUND_ZIP_NAME = 'The delivery area with the zip code %d and the name %s could not be found';
    const NOT_FOUND_ZIP = 'There is no delivery area with the zip code %s';
}