<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 14.05.19
 * Time: 08:17
 */

namespace Pyz\Client\ProductGtin;


use Generated\Shared\Transfer\DriverAppApiRequestTransfer;
use Generated\Shared\Transfer\DriverAppApiResponseTransfer;

interface ProductGtinClientInterface
{
    /**
     * Returns a transfer object containing the mapping between gtins and products
     *
     * @param DriverAppApiRequestTransfer $requestTransfer
     * @return DriverAppApiResponseTransfer
     */
    public function getProductGtins(DriverAppApiRequestTransfer $requestTransfer): DriverAppApiResponseTransfer;
}
