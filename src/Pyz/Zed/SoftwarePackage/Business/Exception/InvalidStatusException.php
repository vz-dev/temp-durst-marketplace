<?php
/**
 * Durst - project - InvalidStatusException.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 26.07.18
 * Time: 15:24
 */

namespace Pyz\Zed\SoftwarePackage\Business\Exception;


class InvalidStatusException extends \Exception
{
    public const MESSAGE = '%s is not a valid status for a software package';
}