<?php
/**
 * Durst - project - SoftwareFeatureExistsException.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 31.10.18
 * Time: 12:41
 */

namespace Pyz\Zed\SoftwarePackage\Business\Exception;


class SoftwareFeatureExistsException extends \Exception
{
    public const EXISTS_ID = 'There is already a software feature with the given id %d';
}
