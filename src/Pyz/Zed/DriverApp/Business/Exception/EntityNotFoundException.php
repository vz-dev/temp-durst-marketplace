<?php
/**
 * Durst - project - EntityNotFoundException.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-08-06
 * Time: 11:30
 */

namespace Pyz\Zed\DriverApp\Business\Exception;

use RuntimeException;

class EntityNotFoundException extends RuntimeException
{
    /**
     * EntityNotFoundException constructor.
     */
    public function __construct(int $idDriverAppRelease)
    {
        parent::__construct(
            sprintf(
                "Entity with id %d could not be found",
                $idDriverAppRelease
            )
        );
    }
}
