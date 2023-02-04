<?php
/**
 * Durst - project - DiscountRequestHandler.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 23.09.20
 * Time: 15:12
 */

namespace Pyz\Yves\AppRestApi\Handler;


use Pyz\Yves\AppRestApi\AppRestApiConfig;
use Pyz\Yves\AppRestApi\Handler\Hydrator\HydratorInterface;
use Pyz\Yves\AppRestApi\Validator\SchemaValidatorTrait;
use stdClass;

class DiscountRequestHandler implements HydratorInterface
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
     * DiscountRequestHandler constructor.
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
     * @param string $json
     * @return \stdClass
     */
    public function handleJson(string $json): stdClass
    {
        $requestObject = json_decode($json);

        $this
            ->validate(
                $requestObject,
                $this
                    ->config
                    ->getDiscountRequestSchemaPath()
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
                    ->getDiscountResponseSchemaPath()
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
                    $responseObject,
                    $version
                );
        }
    }
}
