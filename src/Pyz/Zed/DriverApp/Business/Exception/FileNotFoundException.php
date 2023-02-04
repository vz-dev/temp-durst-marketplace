<?php
/**
 * Durst - project - FileNotFoundException.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-08-07
 * Time: 10:46
 */

namespace Pyz\Zed\DriverApp\Business\Exception;

use RuntimeException;

class FileNotFoundException extends RuntimeException
{
    /**
     * FileNotFoundException constructor.
     *
     * @param string $fileName
     */
    public function __construct(string $fileName)
    {
        parent::__construct(
            sprintf(
                "File %s does not exist",
                $fileName
            )
        );
    }
}
