<?php
/**
 * Durst - project - MerchantTimeSlotRequestHandler.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-10-24
 * Time: 11:35
 */

namespace Pyz\Yves\AppRestApi\Handler;


use Pyz\Yves\AppRestApi\AppRestApiConfig;
use Pyz\Yves\AppRestApi\Handler\Json\Request\MerchantTimeSlotKeyRequestInterface;
use Pyz\Yves\AppRestApi\Log\AnalyticsMerchantTimeSlotFormatter;
use Pyz\Yves\AppRestApi\Validator\SchemaValidatorTrait;
use Spryker\Shared\Log\Config\LoggerConfigInterface;
use Spryker\Shared\Log\LoggerTrait;
use stdClass;

class MerchantTimeSlotRequestHandler implements RequestHandlerInterface
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
     * MerchantTimeSlotRequestHandler constructor.
     * @param \Pyz\Yves\AppRestApi\AppRestApiConfig $config
     * @param \Spryker\Shared\Log\Config\LoggerConfigInterface $loggerConfig
     * @param \Pyz\Yves\AppRestApi\Handler\Hydrator\HydratorInterface[] $hydrators
     */
    public function __construct(
        AppRestApiConfig $config,
        LoggerConfigInterface $loggerConfig,
        array $hydrators
    )
    {
        $this->config = $config;
        $this->loggerConfig = $loggerConfig;
        $this->hydrators = $hydrators;
    }

    /**
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
                    ->getMerchantTimeSlotsRequestSchemaPath()
            );

        if ($this->isValid !== true) {
            return $this
                ->errors;
        }

        $this
            ->log(
                $requestObject
            );

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
                    ->getMerchantTimeSlotsResponseSchemaPath()
            );

        if ($this->isValid !== true) {
            return $this
                ->errors;
        }

        return $responseObject;
    }

    /**
     * @param \stdClass $requestObject
     * @return void
     */
    protected function log(stdClass $requestObject): void
    {
        $cart = [];
        foreach ($requestObject->{MerchantTimeSlotKeyRequestInterface::KEY_CART} as $item) {
            $cart[] = [
                $item->{MerchantTimeSlotKeyRequestInterface::KEY_CART_SKU},
                $item->{MerchantTimeSlotKeyRequestInterface::KEY_CART_QUANTITY},
            ];
        }

        $this->getLogger($this->loggerConfig)
            ->info(
                'merchant time slot request',
                [
                    AnalyticsMerchantTimeSlotFormatter::CONTEXT_ZIP_CODE => $requestObject->{MerchantTimeSlotKeyRequestInterface::KEY_ZIP_CODE},
                    AnalyticsMerchantTimeSlotFormatter::CONTEXT_MERCHANT_ID => $requestObject->{MerchantTimeSlotKeyRequestInterface::KEY_MERCHANT_ID},
                    AnalyticsMerchantTimeSlotFormatter::CONTEXT_CART => $cart,
                ]
            );
    }

    /**
     * @param \stdClass $requestObject
     * @param \stdClass $responseObject
     * @param string $version
     * @return void
     */
    protected function hydrate(stdClass $requestObject, stdClass $responseObject, string $version = 'v1'): void
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
