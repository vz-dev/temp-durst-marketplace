<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 11.06.19
 * Time: 10:50
 */

namespace Pyz\Yves\AppRestApi\Handler\Hydrator\DeliveryArea;


use Pyz\Client\DeliveryArea\DeliveryAreaClientInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Request\CityKeyRequestInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Response\CityKeyResponseInterface;
use stdClass;

class CityHydrator
{
    /**
     * @var DeliveryAreaClientInterface
     */
    protected $client;

    /**
     * CityHydrator constructor.
     * @param DeliveryAreaClientInterface $client
     */
    public function __construct(DeliveryAreaClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param stdClass $requestObject
     * @param stdClass $responseObject
     */
    public function hydrate(stdClass $requestObject, stdClass $responseObject, string$version = 'v1')
    {
        $zip = $this->getZipCode($requestObject);

        $response = $this
            ->client
            ->getCityNameByZipCode($zip);

        if ($response->getZipValid()){
            $responseObject->{CityKeyResponseInterface::KEY_CITY} = $response->getCity();
        }

        $responseObject->{CityKeyResponseInterface::ZIP_VALID} = $response->getZipValid();

    }

    /**
     * @param stdClass $requestObject
     * @return string
     */
    protected function getZipCode(stdClass $requestObject): string
    {
        return $requestObject
            ->{CityKeyRequestInterface::KEY_ZIP_CODE};
    }

}
