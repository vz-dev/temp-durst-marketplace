<?php
/**
 * Durst - project - CodeGeneratorInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 02.12.21
 * Time: 09:32
 */

namespace Pyz\Zed\Merchant\Business\Code;

interface CodeGeneratorInterface
{
    /**
     * @param string $code
     * @return bool
     */
    public function checkCode(string $code): bool;

    /**
     * @return string
     */
    public function generateCode(): string;
}
