<?php
/**
 * Durst - project - BranchNotFoundException.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 28.05.20
 * Time: 09:40
 */

namespace Pyz\Zed\Billing\Business\Exception;

use RuntimeException;

class BranchNotFoundException extends RuntimeException
{
    protected const MESSAGE = 'branch with id %d could not be found';

    /**
     * @param int $idBranch
     *
     * @return static
     */
    public static function build(int $idBranch): self
    {
        return new BranchNotFoundException(
            sprintf(
                static::MESSAGE,
                $idBranch
            )
        );
    }
}
