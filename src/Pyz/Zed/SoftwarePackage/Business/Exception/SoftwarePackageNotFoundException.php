<?php
/**
 * Durst - project - SoftwarePackageNotFoundException.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 26.07.18
 * Time: 14:03
 */

namespace Pyz\Zed\SoftwarePackage\Business\Exception;


class SoftwarePackageNotFoundException extends \Exception
{
    public const MESSAGE = 'software package with code %s could not be found';
    public const MESSAGE_ID = 'software package with id %d could not be found';
}