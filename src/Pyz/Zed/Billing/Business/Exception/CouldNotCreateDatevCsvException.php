<?php
/**
 * Durst - project - CouldNotCreateDatevCsvException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 16.07.20
 * Time: 15:46
 */

namespace Pyz\Zed\Billing\Business\Exception;


use Exception;

class CouldNotCreateDatevCsvException extends Exception
{
    protected const MESSAGE = 'Could not create CSV export with file name %s on destination %s';

    /**
     * @param string $fileName
     * @param string $destinationPath
     * @return static
     */
    public static function build(
        string $fileName,
        string $destinationPath
    ): self
    {
        return new CouldNotCreateDatevCsvException(
            sprintf(
                static::MESSAGE,
                $fileName,
                $destinationPath
            )
        );
    }
}
