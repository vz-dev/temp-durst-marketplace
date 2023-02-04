<?php
/**
 * Durst - project - DeliveryAreaWithZipNotFoundException.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2021-01-27
 * Time: 17:07
 */

namespace Pyz\Zed\DataImport\Business\Exception;


use Spryker\Zed\DataImport\Business\Exception\DataImportException;

class DeliveryAreaWithZipNotFoundException extends DataImportException
{
    public const MESSAGE = 'Delivery Area with zip code %s could not be found';
}
