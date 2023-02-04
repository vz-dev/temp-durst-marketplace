<?php
/**
 * Durst - project - CouldNotCreateZipArchiveException.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 07.04.20
 * Time: 09:13
 */

namespace Pyz\Zed\Billing\Business\Exception;

use Exception;

class CouldNotCreateZipArchiveException extends Exception
{
    protected const MESSAGE = 'Could not create zip archive with file name %s on destination %s';

    /**
     * @param string $fileName
     * @param string $destinationPath
     *
     * @return static
     */
    public static function build(string $fileName, string $destinationPath): self
    {
        return new CouldNotCreateZipArchiveException(
            sprintf(
                static::MESSAGE,
                $fileName,
                $destinationPath
            )
        );
    }
}
