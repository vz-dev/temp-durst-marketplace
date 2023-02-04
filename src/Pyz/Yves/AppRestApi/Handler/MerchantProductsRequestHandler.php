<?php
/**
 * Durst - project - MerchantProductsRequestHandler.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-10-21
 * Time: 13:05
 */

namespace Pyz\Yves\AppRestApi\Handler;


use Pyz\Yves\AppRestApi\AppRestApiConfig;
use Pyz\Yves\AppRestApi\Handler\Hydrator\VersionedHydratorInterface;
use Pyz\Yves\AppRestApi\Validator\SchemaValidatorTrait;
use Spryker\Shared\Log\LoggerTrait;
use stdClass;

class MerchantProductsRequestHandler implements VersionedHydratorInterface
{
    use SchemaValidatorTrait;
    use LoggerTrait;

    /**
     * @var AppRestApiConfig
     */
    protected $config;

    /**
     * @var VersionedHydratorInterface[]
     */
    protected $hydrators;

    /**
     * MerchantProductsRequestHandler constructor.
     * @param AppRestApiConfig $config
     * @param VersionedHydratorInterface[] $hydrators
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
     * @param string $version
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
                    ->getMerchantProductsRequestSchemaPath($version)
            );

        if ($this->isValid !== true) {
            return $this
                ->errors;
        }

        $responseObject = $this
            ->createStdClass();

        $this
            ->hydrate(
                $version,
                $requestObject,
                $responseObject
            );

        $this
            ->validate(
                $responseObject,
                $this
                    ->config
                    ->getMerchantProductsResponseSchemaPath($version)
            );

        if ($this->isValid !== true) {
            return $this
                ->errors;
        }

        return $responseObject;
    }

    /**
     * @param string $version
     * @param stdClass $requestObject
     * @param stdClass $responseObject
     *
     * @return void
     */
    public function hydrate(string $version, stdClass $requestObject, stdClass $responseObject)
    {
        foreach ($this->hydrators as $hydrator) {
            $hydrator
                ->hydrate(
                    $version,
                    $requestObject,
                    $responseObject
                );
        }
    }
}
