<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 14.05.19
 * Time: 08:22
 */

namespace Pyz\Yves\AppRestApi\Handler\Hydrator\DriverApp;

use ArrayObject;
use Generated\Shared\Transfer\DriverAppApiRequestTransfer;
use Pyz\Client\Auth\AuthClientInterface;
use Pyz\Client\ProductGtin\ProductGtinClientInterface;
use Pyz\Yves\AppRestApi\Handler\Hydrator\HydratorInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Request\DriverGtinRequestInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Response\DriverGtinResponseInterface;
use stdClass;

class ProductGtinHydrator implements HydratorInterface
{

    /**
     * @var ProductGtinClientInterface
     */
    protected $productGtinClient;

    /**
     * @var \Pyz\Client\Auth\AuthClientInterface
     */
    protected $authClient;

    /**
     * ProductGtinHydrator constructor.
     * @param \Pyz\Client\ProductGtin\ProductGtinClientInterface $productGtinClient
     * @param \Pyz\Client\Auth\AuthClientInterface $authClient
     */
    public function __construct(
        ProductGtinClientInterface $productGtinClient,
        AuthClientInterface $authClient
    )
    {
        $this->productGtinClient = $productGtinClient;
        $this->authClient = $authClient;
    }

    /**
     * @param \stdClass $requestObject
     * @param \stdClass $responseObject
     */
    public function hydrate(stdClass $requestObject, stdClass $responseObject, string $version = 'v1')
    {
        $authenticated = $this
            ->authenticateDriver($requestObject);

        $responseObject->{DriverGtinResponseInterface::KEY_AUTH_VALID} = $authenticated;

        if ($authenticated !== true) {
            $responseObject->{DriverGtinResponseInterface::KEY_GTINS} = [];
            return;
        }

        $requestTransfer = (new DriverAppApiRequestTransfer())
            ->setToken($this->getToken($requestObject));

        $response = $this
            ->productGtinClient
            ->getProductGtins($requestTransfer);

        $responseObject->{DriverGtinResponseInterface::KEY_GTINS} = $this->hydrateProductGtins($response->getGtins());
    }

    /**
     * @param \stdClass $requestObject
     * @return bool
     */
    protected function authenticateDriver(stdClass $requestObject): bool
    {
        $requestTransfer = (new DriverAppApiRequestTransfer())
            ->setToken($this->getToken($requestObject));

        $response = $this
            ->authClient
            ->authenticateDriver($requestTransfer);

        return $response
            ->getAuthValid();
    }

    /**
     * @param \stdClass $requestObject
     * @return string
     */
    protected function getToken(stdClass $requestObject): string
    {
        return $requestObject
            ->{DriverGtinRequestInterface::KEY_TOKEN};
    }

    /**
     * @param \ArrayObject $gtins
     * @return array
     */
    protected function hydrateProductGtins(ArrayObject $gtins): array
    {
        $responseGtins = [];

        /* @var $gtin \Generated\Shared\Transfer\GtinTransfer */
        foreach ($gtins as $gtin) {
            $currentGtin = new stdClass();

            $currentGtin->{DriverGtinResponseInterface::KEY_GTINS_GTIN} = $gtin->getGtin();
            $currentGtin->{DriverGtinResponseInterface::KEY_GTINS_PRODUCT_NAME} = $gtin->getProductName();

            $skus = $gtin->getSkus();
            foreach ($skus as $sku) {
                $currentSku = new stdClass();
                $currentSku->{DriverGtinResponseInterface::KEY_GTINS_SKU} = $sku->getSku();
                $currentSku->{DriverGtinResponseInterface::KEY_GTINS_DEPOSIT_ID} = $sku->getFkDeposit();
                $currentGtin->{DriverGtinResponseInterface::KEY_GTINS_SKUS}[] = $currentSku;
            }

            $responseGtins[] = $currentGtin;
        }

        return $responseGtins;
    }
}
