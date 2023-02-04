<?php
/**
 * Durst - project - DriverDownloadLatestReleaseHandler.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-08-12
 * Time: 13:54
 */

namespace Pyz\Yves\AppRestApi\Handler;


use Generated\Shared\Transfer\DriverAppApiRequestTransfer;
use Pyz\Client\Auth\AuthClientInterface;
use Pyz\Client\DriverApp\DriverAppClientInterface;
use Pyz\Yves\AppRestApi\AppRestApiConfig;
use Pyz\Yves\AppRestApi\Exception\AccessDeniedException;
use Pyz\Yves\AppRestApi\Exception\NoDriverAppReleaseException;
use Pyz\Yves\AppRestApi\Handler\Json\Request\DriverDownloadLatestReleaseRequestInterface as Request;
use Pyz\Yves\AppRestApi\Validator\SchemaValidatorTrait;
use Spryker\Shared\Log\LoggerTrait;
use stdClass;

class DriverDownloadLatestReleaseHandler
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
    public function handleJson(string $json, string $version = 'v1'): string
    {
        $requestObject = json_decode($json);

        $this
            ->validate(
                $requestObject,
                $this
                    ->config
                    ->getDriverDownloadLatestReleaseRequestSchemaPath()
            );

        if ($this->isValid !== true) {
            return $this
                ->errors;
        }

        if($this->authenticate($requestObject) !== true){
            throw new AccessDeniedException();
        }

        $release = $this
            ->client
            ->getLatestRelease();

        if($release->getVersion() == null){
            throw new NoDriverAppReleaseException();
        }

        return sprintf(
            '%s/%s',
            $this->config->getDriverAppApkUploadPath(),
            $release->getApkFilePath()
        );
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
