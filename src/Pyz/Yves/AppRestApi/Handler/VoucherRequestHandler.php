<?php
/**
 * Durst - project - VoucherRequestHandler.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 08.05.18
 * Time: 16:41
 */

namespace Pyz\Yves\AppRestApi\Handler;

use Pyz\Yves\AppRestApi\AppRestApiConfig;
use Pyz\Yves\AppRestApi\Handler\Hydrator\HydratorInterface;
use Pyz\Yves\AppRestApi\Validator\SchemaValidatorTrait;

class VoucherRequestHandler implements RequestHandlerInterface, HydratorInterface
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
     * VoucherRequestHandler constructor.
     * @param AppRestApiConfig $config
     * @param HydratorInterface[] $hydrators
     */
    public function __construct(AppRestApiConfig $config, array $hydrators)
    {
        $this->config = $config;
        $this->hydrators = $hydrators;
    }

    /**
     * @param string $json
     * @return \stdClass
     * @throws \Pyz\Yves\AppRestApi\Exception\InvalidJsonException
     */
    public function handleJson(string $json, string $version = 'v1') : \stdClass
    {
        $requestObject = json_decode($json);

        $this->validate($requestObject, $this->config->getVoucherRequestSchemaPath());
        if ($this->isValid !== true) {
            return $this->errors;
        }

        $responseObject = $this->createStdClass();
        $this->hydrate($requestObject, $responseObject);

        $this->validate($responseObject, $this->config->getVoucherResponseSchemaPath());
        if ($this->isValid !== true) {
            return $this->errors;
        }

        return $responseObject;
    }

    /**
     * @param \stdClass $requestObject
     * @param \stdClass $responseObject
     * @return void
     */
    public function hydrate(\stdClass $requestObject, \stdClass $responseObject, string $version = 'v1')
    {
        foreach($this->hydrators as $hydrator){
            $hydrator->hydrate($requestObject, $responseObject, $version);
        }
    }
}
