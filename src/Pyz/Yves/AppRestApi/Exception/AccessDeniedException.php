<?php
/**
 * Durst - project - AccessDeniedException.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-08-12
 * Time: 14:05
 */

namespace Pyz\Yves\AppRestApi\Exception;

use RuntimeException;

class AccessDeniedException extends RuntimeException
{
    /**
     * AccessDeniedException constructor.
     */
    public function __construct()
    {
        parent::__construct('Access denied');
    }
}
