<?php
/**
 * Durst - project - CitynameHydrator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-10-18
 * Time: 12:52
 */

namespace Pyz\Yves\AppRestApi\Handler\Hydrator\City;


use Pyz\Client\DeliveryArea\DeliveryAreaClientInterface;
use Pyz\Yves\AppRestApi\Handler\Hydrator\HydratorInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Request\CityMerchantKeyRequestInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Response\CityMerchantKeyResponseInterface;
use stdClass;

class CitynameHydrator implements HydratorInterface
{
    /**
     * @var \Pyz\Client\DeliveryArea\DeliveryAreaClientInterface
     */
    protected $client;

    /**
     * CitynameHydrator constructor.
     * @param \Pyz\Client\DeliveryArea\DeliveryAreaClientInterface $client
     */
    public function __construct(
        DeliveryAreaClientInterface $client
    )
    {
        $this->client = $client;
    }

    /**
     * @param \stdClass $requestObject
     * @param \stdClass $responseObject
     *
     * @return void
     */
    public function hydrate(stdClass $requestObject, stdClass $responseObject, string $version = 'v1'): void
    {
        $zipCode = $this
            ->getZipCode($requestObject);

        $branchCode = $this
            ->getBranchCode($requestObject);

        $response = $this
            ->client
            ->getCityNameByZipOrBranchCode(
                $zipCode,
                $branchCode
            );

        $cityName = null;

        if ($response->getZipValid() === true) {
            $cityName = $response
                ->getCity();
        }

        $responseObject
            ->{CityMerchantKeyResponseInterface::KEY_CITY} = $cityName;
    }

    /**
     * @param \stdClass $requestObject
     * @return string
     */
    protected function getZipCode(stdClass $requestObject): string
    {
        return $requestObject
            ->{CityMerchantKeyRequestInterface::KEY_ZIP_CODE};
    }

    /**
     * @param \stdClass $requestObject
     * @return string
     */
    protected function getBranchCode(stdClass $requestObject): string
    {
        return $requestObject
            ->{CityMerchantKeyRequestInterface::KEY_BRANCH_CODE};
    }
}
