<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 15.01.19
 * Time: 11:18
 */

namespace Pyz\Zed\Oms\Business\Model\Durst;


use Generated\Shared\Transfer\DurstCompanyTransfer;

interface DurstCompanyDetailsManagerInterface
{
    /**
     * @return DurstCompanyTransfer
     */
    public function createDurstCompanyTransfer() : DurstCompanyTransfer;
}
