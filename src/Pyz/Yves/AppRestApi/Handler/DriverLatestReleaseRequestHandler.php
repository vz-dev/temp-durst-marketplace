<?php
/**
 * Durst - project - DriverLatestReleaseRequestHandler.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-08-07
 * Time: 12:47
 */

namespace Pyz\Yves\AppRestApi\Handler;

use Generated\Shared\Transfer\DriverAppApiRequestTransfer;
use Pyz\Client\Auth\AuthClientInterface;
use Pyz\Client\DriverApp\DriverAppClientInterface;
use Pyz\Yves\AppRestApi\AppRestApiConfig;
use Pyz\Yves\AppRestApi\Handler\Json\Request\DriverLatestReleaseRequestInterface as Request;
use Pyz\Yves\AppRestApi\Handler\Json\Response\DriverLatestReleaseResponseInterface as Response;
use Pyz\Yves\AppRestApi\Validator\SchemaValidatorTrait;
use Spryker\Shared\Log\LoggerTrait;
use stdClass;

class DriverLatestReleaseRequestHandler implements RequestHandlerInterface
{
    use SchemaValidatorTrait;
    use LoggerTrait;

    /**
     * @var \Pyz\Yves\AppRestApi\AppRestApiConfig
     */
    protected $config;

    /**
     * @var \Pyz\Client\DriverApp\DriverAppClientInterface
     */
    protected $client;

    /**
     * @var \Pyz\Client\Auth\AuthClientInterface
     */
    protected $authClient;

    /**
     * DriverLatestReleaseRequestHandler constructor.
     *
     * @param \Pyz\Yves\AppRestApi\AppRestApiConfig $config
     * @param \Pyz\Client\DriverApp\DriverAppClientInterface $client
     * @param \Pyz\Client\Auth\AuthClientInterface $authClient
     */
    public function __construct(
        AppRestApiConfig $config,
        DriverAppClientInterface $client,
        AuthClientInterface $authClient
    ) {
        $this->config = $config;
        $this->client = $client;
        $this->authClient = $authClient;
    }

    /**
     * @param string $json
     *
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
                    ->getDriverLatestReleaseRequestSchemaPath()
            );

        if ($this->isValid !== true) {
            return $this
                ->errors;
        }

        $responseObject = $this->createStdClass();

        $this
            ->hydrate($requestObject, $responseObject);

        $this
            ->validate(
                $responseObject,
                $this
                    ->config
                    ->getDriverLatestReleaseResponseSchemaPath()
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
     *
     * @return void
     */
    protected function hydrate(stdClass $requestObject, stdClass $responseObject): void
    {
        $responseObject->{Response::KEY_AUTH_VALID} = $this
            ->authenticate($requestObject);

        if($responseObject->{Response::KEY_AUTH_VALID} !== true){
            $responseObject->{Response::KEY_VERSION} = null;
            return;
        }

        $release = $this
            ->client
            ->getLatestRelease();

        $responseObject->{Response::KEY_VERSION} = $release->getVersion();
    }

    /**
     * @param \stdClass $requestObject
     *
     * @return bool
     */
    protected function authenticate(stdClass $requestObject): bool
    {
        $requestTransfer = (new DriverAppApiRequestTransfer())
            ->setToken($requestObject->{Request::KEY_TOKEN});

        return $this
            ->authClient
            ->authenticateDriver($requestTransfer)
            ->getAuthValid();
    }
}
