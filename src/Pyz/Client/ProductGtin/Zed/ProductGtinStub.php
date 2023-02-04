<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 14.05.19
 * Time: 08:14
 */

namespace Pyz\Client\ProductGtin\Zed;


use Generated\Shared\Transfer\DriverAppApiRequestTransfer;
use Generated\Shared\Transfer\DriverAppApiResponseTransfer;
use Spryker\Client\ZedRequest\ZedRequestClientInterface;
use Spryker\Shared\Kernel\Transfer\TransferInterface;

class ProductGtinStub implements ProductGtinStubInterface
{
    protected const URL_DRIVER_APP_GTIN = '/product/gateway/get-all-product-gtins-for-driver-app';

    /**
     * @var \Spryker\Client\ZedRequest\ZedRequestClientInterface
     */
    protected $zedStub;

    /**
     * ProductGtinStub constructor.
     * @param ZedRequestClientInterface $zedStub
     */
    public function __construct(ZedRequestClientInterface $zedStub)
    {
        $this->zedStub = $zedStub;
    }

    /**
     * @param DriverAppApiRequestTransfer $requestTransfer
     * @return DriverAppApiResponseTransfer|TransferInterface
     */
    public function getProductGtins(DriverAppApiRequestTransfer $requestTransfer): DriverAppApiResponseTransfer
    {
        return $this
            ->zedStub
            ->call(
                self::URL_DRIVER_APP_GTIN,
                $requestTransfer,
                null
            );

    }
}
