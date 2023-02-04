<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-03-07
 * Time: 16:22
 */

namespace Pyz\Zed\Discount\Business\Model;

interface DiscountDisplayNameGeneratorInterface
{
    /**
     * @param int $idBranch
     * @return string
     */
    public function generateDisplayName(int $idBranch): string;
}