<?php
/**
 * Durst - project - DeliveryAreaHandler.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 16.03.21
 * Time: 09:46
 */

namespace Pyz\Yves\AppRestApi\Handler;


use Pyz\Yves\AppRestApi\AppRestApiConfig;
use Pyz\Yves\AppRestApi\Handler\Hydrator\HydratorInterface;
use Pyz\Yves\AppRestApi\Validator\SchemaValidatorTrait;
use stdClass;

class DeliveryAreaRequestHandler implements RequestHandlerInterface
{
    use SchemaValidatorTrait;

    /**
     * @var \Pyz\Yves\AppRestApi\AppRestApiConfig
     */
    protected $config;

    /**
     * @var HydratorInterface[]
     */
    protected $hydrators;

    /**
     * DeliveryAreaHandler constructor.
     * @param \Pyz\Yves\AppRestApi\AppRestApiConfig $config
     * @param \Pyz\Yves\AppRestApi\Handler\Hydrator\HydratorInterface[] $hydrators
     */
    public function __construct(
        AppRestApiConfig $config,
        array $hydrators
    )
    {
        $this->config = $config;
        $this->hydrators = $hydrators;
    }

    /**
     * {@inheritDoc}
     *
     * @param string $json
     * @return \stdClass
     */
    public function handleJson(string $json, string $version = 'v1'): stdClass
    {
        $requestObject = json_decode($json);

        $this
            ->validate(
                $requestObject,
                $this
                    ->config
                    ->getDeliveryAreaRequestSchemaPath()
            );

        if ($this->isValid !== true) {
            return $this
                ->errors;
        }

        $responseObject = $this
            ->createStdClass();

        $this
            ->hydrate(
                $requestObject,
                $responseObject
            );

        $this
            ->validate(
                $responseObject,
                $this
                    ->config
                    ->getDeliveryAreaResponseSchemaPath()
            );

        if ($this->isValid !== true) {
            return $this
                ->errors;
        }

        return $responseObject;
    }

    /**
     * @param \stdClass $requestObject
     * @param \stdClass $responseObject
     * @return void
     */
    public function hydrate(stdClass $requestObject, stdClass $responseObject, string $version = 'v1'): void
    {
        foreach ($this->hydrators as $hydrator) {
            $hydrator
                ->hydrate(
                    $requestObject,
                    $responseObject
                );
        }
    }
}
