<?php
/**
 * Durst - project - GraphmastersSettingsRequestHandler.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 13.01.22
 * Time: 17:45
 */

namespace Pyz\Yves\AppRestApi\Handler;


use Pyz\Yves\AppRestApi\AppRestApiConfig;
use Pyz\Yves\AppRestApi\Handler\Hydrator\HydratorInterface;
use Pyz\Yves\AppRestApi\Validator\SchemaValidatorTrait;
use stdClass;

class GraphmastersSettingsRequestHandler implements RequestHandlerInterface
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
     * MerchantTimeSlotRequestHandler constructor.
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
     * @param string $version
     * @return stdClass
     */
    public function handleJson(string $json, string $version = 'v1'): stdClass
    {
        $requestObject = json_decode($json);

        $responseObject = $this
            ->createStdClass();

        $this
            ->hydrate(
                $requestObject,
                $responseObject
            );

       /*
        ->validate(
                $responseObject,
                $this
                    ->config
                    ->getEvaluateTimeSlotsResponseSchemaPath()
            );

        if ($this->isValid !== true) {
            return $this
                ->errors;
        }
       */

        return $responseObject;
    }

    /**
     * @param stdClass $requestObject
     * @param stdClass $responseObject
     * @return void
     */
    protected function hydrate(stdClass $requestObject, stdClass $responseObject): void
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
