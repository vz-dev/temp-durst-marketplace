<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 14.05.19
 * Time: 08:15
 */

namespace Pyz\Client\ProductGtin;


use Generated\Shared\Transfer\DriverAppApiRequestTransfer;
use Generated\Shared\Transfer\DriverAppApiResponseTransfer;
use Spryker\Client\Kernel\AbstractClient;

/**
 * Class ProductGtinClient
 * @package Pyz\Client\ProductGtin
 * @method ProductGtinFactory getFactory()
 */
class ProductGtinClient extends AbstractClient implements ProductGtinClientInterface
{
    /**
     * {@inheritDoc}
     *
     * @param DriverAppApiRequestTransfer $requestTransfer
     * @return DriverAppApiResponseTransfer
     * @throws \Spryker\Client\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getProductGtins(DriverAppApiRequestTransfer $requestTransfer): DriverAppApiResponseTransfer
    {
        return $this
            ->getFactory()
            ->createProductGtinStub()
            ->getProductGtins($requestTransfer);
    }

}
