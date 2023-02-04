<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 11.06.19
 * Time: 10:28
 */

namespace Pyz\Yves\AppRestApi\Handler;

use Pyz\Yves\AppRestApi\AppRestApiConfig;

use Pyz\Yves\AppRestApi\Validator\SchemaValidatorTrait;
use stdClass;

class CityRequestHandler implements RequestHandlerInterface
{
    use SchemaValidatorTrait;

    /**
     * @var AppRestApiConfig
     */
    protected $config;

    /**
     * @var array
     */
    protected $hydrators;

    /**
     * CityRequestHandler constructor.
     * @param AppRestApiConfig $config
     * @param array $hydrators
     */
    public function __construct(AppRestApiConfig $config, array $hydrators)
    {
        $this->config = $config;
        $this->hydrators = $hydrators;
    }

    public function handleJson(string $json, string $version = 'v1'): stdClass
    {
        $requestObject = json_decode($json);

        $this
            ->validate(
                $requestObject,
                $this
                    ->config
                    ->getCityRequestSchemaPath()
            );

        if ($this->isValid !== true) {
            return $this
                ->errors;
        }

        $responseObject = $this->createStdClass();

        $this
            ->hydrate($requestObject, $responseObject);

        $this
            ->validate(
                $responseObject,
                $this
                    ->config
                    ->getCityResponseSchemaPath()
            );

        if ($this->isValid !== true) {
            return $this
                ->errors;
        }

        return $responseObject;
    }

    /**
     * @param stdClass $requestObject
     * @param stdClass $responseObject
     */
    protected function hydrate(stdClass $requestObject, stdClass $responseObject): void
    {
        foreach ($this->hydrators as $hydrator) {
            $hydrator->hydrate($requestObject, $responseObject);
        }
    }



}
