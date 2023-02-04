<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 21.02.18
 * Time: 11:27
 */

namespace Pyz\Yves\AppRestApi\Handler;

use Pyz\Yves\AppRestApi\AppRestApiConfig;
use Pyz\Yves\AppRestApi\Handler\Hydrator\HydratorInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Request\BranchKeyRequestInterface as Request;
use Pyz\Yves\AppRestApi\Log\AnalyticsBranchFormatter;
use Pyz\Yves\AppRestApi\Validator\SchemaValidatorTrait;
use Spryker\Shared\Log\Config\LoggerConfigInterface;
use Spryker\Shared\Log\LoggerTrait;

class BranchRequestHandler implements HydratorInterface
{
    use SchemaValidatorTrait;
    use LoggerTrait;

    /**
     * @var AppRestApiConfig
     */
    protected $config;

    /**
     * @var HydratorInterface[]
     */
    protected $hydrators;

    /**
     * @var LoggerConfigInterface
     */
    protected $loggerConfig;

    /**
     * BranchRequestHandler constructor.
     * @param AppRestApiConfig $config
     * @param HydratorInterface[] $hydrators
     * @param LoggerConfigInterface $loggerConfig
     */
    public function __construct(
        AppRestApiConfig $config,
        array $hydrators,
        LoggerConfigInterface $loggerConfig
    )
    {
        $this->config = $config;
        $this->hydrators = $hydrators;
        $this->loggerConfig = $loggerConfig;
    }

    /**
     * @param string $json
     * @return \stdClass
     */
    public function handleJson(string $json) : \stdClass
    {
        $requestObject = json_decode($json);

        $this->validate($requestObject, $this->config->getBranchRequestSchemaPath());
        if($this->isValid !== true){
            return $this->errors;
        }

        $this->log($requestObject);

        $responseObject = $this->createStdClass();

        $this->hydrate($requestObject, $responseObject);

        $this->validate($responseObject, $this->config->getBranchResponseSchemaPath());
        if($this->isValid !== true){
            return $this->errors;
        }

        return $responseObject;
    }

    /**
     * @param \stdClass $requestObject
     */
    protected function log(\stdClass $requestObject)
    {
        $this->getLogger($this->loggerConfig)
            ->info(
                'branchRequest',
                [
                    AnalyticsBranchFormatter::CONTEXT_ZIP_CODE => $requestObject->{Request::KEY_ZIP_CODE},
                    AnalyticsBranchFormatter::CONTEXT_MERCHANT_ID => $requestObject->{Request::KEY_MERCHANT_ID},
                ]
            );
    }

    /**
     * @param \stdClass $requestObject
     * @param \stdClass $responseObject
     * @return mixed|void
     */
    public function hydrate(\stdClass $requestObject, \stdClass $responseObject, string $version = 'v1')
    {
        foreach ($this->hydrators as $hydrator) {
            $hydrator->hydrate($requestObject, $responseObject, $version);
        }
    }

}
