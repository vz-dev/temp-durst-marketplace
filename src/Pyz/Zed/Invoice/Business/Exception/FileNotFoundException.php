<?php
/**
 * Durst - project - FileNotFoundException.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 07.04.20
 * Time: 08:50
 */

namespace Pyz\Zed\Invoice\Business\Exception;

use Exception;

class FileNotFoundException extends Exception
{
    protected const MESSAGE = 'The file %s does not exist';

    /**
     * @param string $filePath
     *
     * @return static
     */
    public static function build(string $filePath): self
    {
        return new FileNotFoundException(
            sprintf(
                static::MESSAGE,
                $filePath
            )
        );
    }
}
