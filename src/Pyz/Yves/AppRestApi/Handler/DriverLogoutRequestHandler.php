<?php


namespace Pyz\Yves\AppRestApi\Handler;


use Pyz\Yves\AppRestApi\AppRestApiConfig;
use Pyz\Yves\AppRestApi\Handler\Json\Response\DriverLoginResponseInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Response\DriverLogoutResponseInterface;
use Pyz\Yves\AppRestApi\Validator\SchemaValidatorTrait;
use Spryker\Shared\Log\LoggerTrait;
use stdClass;

class DriverLogoutRequestHandler implements RequestHandlerInterface
{
    use SchemaValidatorTrait;
    use LoggerTrait;

    /**
     * @var AppRestApiConfig
     */
    protected $config;

    /**
     * @var array
     */
    protected $hydrators;

    /**
     * DriverLogoutRequestHandler constructor.
     * @param \Pyz\Yves\AppRestApi\AppRestApiConfig $config
     * @param array $hydrators
     */
    public function __construct(AppRestApiConfig $config, array $hydrators)
    {
        $this->config = $config;
        $this->hydrators = $hydrators;
    }

    /**
     * @param string $json
     * @return stdClass
     */
    public function handleJson(string $json, string $version = 'v1'): stdClass
    {
        $requestObject = json_decode($json);

        $this
            ->validate(
                $requestObject,
                $this
                    ->config
                    ->getDriverLogoutRequestSchemaPath()
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
                    ->getDriverLogoutResponseSchemaPath()
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
