<?php
/**
 * Durst - project - CancelOrderManagerInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 07.09.21
 * Time: 15:13
 */

namespace Pyz\Zed\CancelOrder\Business\Manager;

use Generated\Shared\Transfer\JwtTransfer;

/**
 * Interface CancelOrderManagerInterface
 * @package Pyz\Zed\CancelOrder\Business\Manager
 */
interface CancelOrderManagerInterface
{
    /**
     * @param string|null $token
     * @return \Generated\Shared\Transfer\JwtTransfer
     */
    public function prepareTriggerFromToken(
        ?string $token = null
    ): JwtTransfer;
}
