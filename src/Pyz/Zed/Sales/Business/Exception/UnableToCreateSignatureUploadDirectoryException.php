<?php
/**
 * Durst - project - UnableToCreateSignatureUploadDirectoryException.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-16
 * Time: 10:30
 */

namespace Pyz\Zed\Sales\Business\Exception;

use RuntimeException;

class UnableToCreateSignatureUploadDirectoryException extends RuntimeException
{
    public const MESSAGE = 'The signature directory %s could not be created';
}
