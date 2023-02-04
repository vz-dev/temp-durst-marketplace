<?php

namespace Pyz\Yves\AppRestApi\Handler;

use Pyz\Yves\AppRestApi\AppRestApiConfig;
use Pyz\Yves\AppRestApi\Handler\Hydrator\HydratorInterface;
use Pyz\Yves\AppRestApi\Validator\SchemaValidatorTrait;
use stdClass;

class MerchantProductRequestHandler implements HydratorInterface
{
    use SchemaValidatorTrait;

    /**
     * @var AppRestApiConfig
     */
    protected $config;

    /**
     * @var HydratorInterface[]
     */
    protected $hydrators;

    /**
     * @param AppRestApiConfig $config
     * @param HydratorInterface[] $hydrators
     */
    public function __construct(AppRestApiConfig $config, array $hydrators)
    {
        $this->config = $config;
        $this->hydrators = $hydrators;
    }

    /**
     * @param string $requestContent
     * @return stdClass
     */
    public function handleRequest(string $requestContent): stdClass
    {
        $requestObject = json_decode($requestContent);

        $this
            ->validate(
                $requestObject,
                $this
                    ->config
                    ->getMerchantProductRequestSchemaPath()
            );

        if ($this->isValid !== true) {
            return $this
                ->errors;
        }

        $responseObject = $this->createStdClass();

        $this->hydrate($requestObject, $responseObject);

        $this->validate(
            $responseObject,
            $this
                ->config
                ->getMerchantProductResponseSchemaPath()
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
     *
     * @return void
     */
    public function hydrate(stdClass $requestObject, stdClass $responseObject, string $version = 'v1')
    {
        foreach ($this->hydrators as $hydrator) {
            $hydrator
                ->hydrate(
                    $requestObject,
                    $responseObject,
                    $version
                );
        }
    }
}
