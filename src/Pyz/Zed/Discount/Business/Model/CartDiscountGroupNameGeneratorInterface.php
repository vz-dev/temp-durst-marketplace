<?php
/**
 * Durst - project - CartDiscountGroupNameGeneratorInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 12.04.21
 * Time: 09:40
 */

namespace Pyz\Zed\Discount\Business\Model;


interface CartDiscountGroupNameGeneratorInterface
{
    /**
     * @param int $idBranch
     * @return string
     */
    public function generateGroupName(int $idBranch): string;
}
