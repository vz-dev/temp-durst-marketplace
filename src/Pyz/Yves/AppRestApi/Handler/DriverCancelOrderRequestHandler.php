<?php
/**
 * Durst - project - DriverCancelOrderRequestHandler.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 13.09.21
 * Time: 13:58
 */

namespace Pyz\Yves\AppRestApi\Handler;

use Pyz\Yves\AppRestApi\AppRestApiConfig;
use Pyz\Yves\AppRestApi\Validator\SchemaValidatorTrait;
use stdClass;

class DriverCancelOrderRequestHandler implements RequestHandlerInterface
{
    use SchemaValidatorTrait;

    /**
     * @var \Pyz\Yves\AppRestApi\AppRestApiConfig
     */
    protected $config;

    /**
     * @var array
     */
    protected $hydrators;

    /**
     * @param \Pyz\Yves\AppRestApi\AppRestApiConfig $config
     * @param array $hydrators
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
     * @param string $version
     * @return \stdClass
     */
    public function handleJson(
        string $json,
        string $version = 'v1'
    ): \stdClass
    {
        $requestObject = json_decode($json);

        $this
            ->validate(
                $requestObject,
                $this
                    ->config
                    ->getDriverCancelOrderRequestSchemaPath()
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
                $responseObject,
                $version
            );

        $this
            ->validate(
                $responseObject,
                $this
                    ->config
                    ->getDriverCancelOrderResponseSchemaPath()
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
     * @param string $version
     * @return void
     */
    protected function hydrate(
        stdClass $requestObject,
        stdClass $responseObject,
        string $version = 'v1'
    ): void
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
