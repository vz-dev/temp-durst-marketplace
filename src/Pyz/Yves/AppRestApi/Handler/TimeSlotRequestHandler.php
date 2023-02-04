<?php
/**
 * Durst - project - TimeSlotRequestHandler.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 23.04.18
 * Time: 14:28
 */

namespace Pyz\Yves\AppRestApi\Handler;

use Pyz\Yves\AppRestApi\AppRestApiConfig;
use Pyz\Yves\AppRestApi\Handler\Json\Request\TimeSlotKeyRequestInterface as Request;
use Pyz\Yves\AppRestApi\Log\AnalyticsTimeSlotFormatter;
use Pyz\Yves\AppRestApi\Validator\SchemaValidatorTrait;
use Spryker\Shared\Log\Config\LoggerConfigInterface;
use Spryker\Shared\Log\LoggerTrait;
use stdClass;

class TimeSlotRequestHandler implements RequestHandlerInterface
{
    use SchemaValidatorTrait;
    use LoggerTrait;

    /**
     * @var \Pyz\Yves\AppRestApi\AppRestApiConfig
     */
    protected $config;

    /**
     * @var \Spryker\Shared\Log\Config\LoggerConfigInterface
     */
    protected $loggerConfig;

    /**
     * @var \Pyz\Yves\AppRestApi\Handler\Hydrator\HydratorInterface[]
     */
    protected $hydrators;

    /**
     * TimeSlotRequestHandler constructor.
     *
     * @param \Pyz\Yves\AppRestApi\AppRestApiConfig $config
     * @param \Spryker\Shared\Log\Config\LoggerConfigInterface $loggerConfig
     * @param \Pyz\Yves\AppRestApi\Handler\Hydrator\HydratorInterface[] $hydrators
     */
    public function __construct(
        AppRestApiConfig $config,
        LoggerConfigInterface $loggerConfig,
        array $hydrators
    ) {
        $this->config = $config;
        $this->loggerConfig = $loggerConfig;
        $this->hydrators = $hydrators;
    }

    /**
     * @param string $json
     *
     * @return \stdClass
     */
    public function handleJson(string $json, string $version = 'v1'): stdClass
    {
        $requestObject = json_decode($json);

        $this->validate($requestObject, $this->config->getTimeSlotRequestSchemaPath());
        if ($this->isValid !== true) {
            return $this->errors;
        }

        $this->log($requestObject);

        $responseObject = $this->createStdClass();

        $this->hydrate($requestObject, $responseObject);

        $this->validate($responseObject, $this->config->getTimeSlotResponseSchemaPath());
        if ($this->isValid !== true) {
            return $this->errors;
        }

        return $responseObject;
    }

    /**
     * @param \stdClass $requestObject
     *
     * @return void
     */
    protected function log(stdClass $requestObject)
    {
        $cart = [];
        foreach ($requestObject->{Request::KEY_CART} as $item) {
            $cart[] = [
                $item->{Request::KEY_CART_SKU},
                $item->{Request::KEY_CART_QUANTITY},
            ];
        }

        $this->getLogger($this->loggerConfig)
            ->info(
                'time slot request',
                [
                    AnalyticsTimeSlotFormatter::CONTEXT_ZIP_CODE => $requestObject->{Request::KEY_ZIP_CODE},
                    AnalyticsTimeSlotFormatter::CONTEXT_MERCHANT_IDS => $requestObject->{Request::KEY_MERCHANT_IDS},
                    AnalyticsTimeSlotFormatter::CONTEXT_CART => $cart,
                ]
            );
    }

    /**
     * @param \stdClass $requestObject
     * @param \stdClass $responseObject
     *
     * @return void
     */
    protected function hydrate(stdClass $requestObject, stdClass $responseObject)
    {
        foreach ($this->hydrators as $hydrator) {
            $hydrator->hydrate($requestObject, $responseObject);
        }
    }
}
