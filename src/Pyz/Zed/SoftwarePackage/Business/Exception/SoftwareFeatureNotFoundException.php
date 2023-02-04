<?php
/**
 * Durst - project - SoftwareFeatureNotFoundException.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 31.10.18
 * Time: 12:43
 */

namespace Pyz\Zed\SoftwarePackage\Business\Exception;


class SoftwareFeatureNotFoundException extends \Exception
{
    public const NOT_FOUND = 'A software-feature with the id %d could not be found';
    public const NO_ID = 'You are trying to update a software-feature without an id';
    public const CODE_NOT_FOUND = 'software-feature with code %s could not be found';
}