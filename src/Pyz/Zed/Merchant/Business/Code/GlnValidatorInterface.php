<?php
/**
 * Durst - project - GlnValidatorInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 02.12.21
 * Time: 09:34
 */

namespace Pyz\Zed\Merchant\Business\Code;

interface GlnValidatorInterface
{
    /**
     * @param string $gln
     * @return bool
     */
    public function validate(string $gln): bool;
}
