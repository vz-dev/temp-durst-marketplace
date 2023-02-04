<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 14.05.19
 * Time: 08:15
 */

namespace Pyz\Client\ProductGtin\Zed;


use Generated\Shared\Transfer\DriverAppApiRequestTransfer;
use Generated\Shared\Transfer\DriverAppApiResponseTransfer;

interface ProductGtinStubInterface
{
    /**
     * @param DriverAppApiRequestTransfer $requestTransfer
     * @return DriverAppApiResponseTransfer
     */
    public function getProductGtins(DriverAppApiRequestTransfer $requestTransfer): DriverAppApiResponseTransfer;

}
