<?php
/**
 * Durst - project - OverviewRequestHandler.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-11-05
 * Time: 14:26
 */

namespace Pyz\Yves\AppRestApi\Handler;


use Pyz\Yves\AppRestApi\AppRestApiConfig;
use Pyz\Yves\AppRestApi\Handler\Hydrator\HydratorInterface;
use Pyz\Yves\AppRestApi\Validator\SchemaValidatorTrait;
use Spryker\Shared\Log\Config\LoggerConfigInterface;
use stdClass;

class OverviewRequestHandler implements RequestHandlerInterface
{
    use SchemaValidatorTrait;

    /**
     * @var AppRestApiConfig
     */
    protected $config;

    /**
     * @var LoggerConfigInterface
     */
    protected $loggerConfig;

    /**
     * @var HydratorInterface[]
     */
    protected $hydrators;

    /**
     * OverviewRequestHandler constructor.
     * @param AppRestApiConfig $config
     * @param HydratorInterface[] $hydrators
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
                    ->getOverviewRequestSchemaPath()
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
                    ->getOverviewResponseSchemaPath()
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
     * @return void
     */
    protected function hydrate(stdClass $requestObject, stdClass $responseObject, string $version): void
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
