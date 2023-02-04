<?php
/**
 * Durst - project - FileRemoveException.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-08-07
 * Time: 10:24
 */

namespace Pyz\Zed\DriverApp\Business\Exception;

use RuntimeException;

class FileRemoveException extends RuntimeException
{
    /**
     * FileRemoveException constructor.
     *
     * @param string $fileName
     */
    public function __construct(string $fileName)
    {
        parent::__construct(
            sprintf(
                "Cannot remove file %s",
                $fileName
            )
        );
    }
}
