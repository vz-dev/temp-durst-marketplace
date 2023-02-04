<?php


namespace Pyz\Yves\AppRestApi\Handler\Hydrator\DriverApp;


use Generated\Shared\Transfer\DriverAppApiRequestTransfer;
use Pyz\Client\Auth\AuthClientInterface;
use Pyz\Yves\AppRestApi\Handler\Hydrator\HydratorInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Request\DriverLoginRequestInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Response\DriverLoginResponseInterface;
use stdClass;

class LoginHydrator implements HydratorInterface
{
    /**
     * @var \Pyz\Client\Auth\AuthClientInterface
     */
    protected $authClient;

    /**
     * LoginHydrator constructor.
     * @param \Pyz\Client\Auth\AuthClientInterface $authClient
     */
    public function __construct(AuthClientInterface $authClient)
    {
        $this->authClient = $authClient;
    }

    /**
     * @param \stdClass $requestObject
     * @param \stdClass $responseObject
     *
     * @return void
     */
    public function hydrate(stdClass $requestObject, stdClass $responseObject, string $version = 'v1')
    {
        $requestTransfer = (new DriverAppApiRequestTransfer())
            ->setEmail($this->getEmail($requestObject))
            ->setPassword($this->getPassword($requestObject));

        $response = $this
            ->authClient
            ->loginDriver($requestTransfer);

        if($response->getAuthValid() === true){

            $requestTransfer = (new DriverAppApiRequestTransfer())
                ->setToken($response->getToken());
            $driver = $this
                ->authClient
                ->getDriverByToken($requestTransfer);

            $responseObject->{DriverLoginResponseInterface::KEY_FIRST_NAME} = $driver->getFirstName();
            $responseObject->{DriverLoginResponseInterface::KEY_LAST_NAME} = $driver->getLastName();
            $responseObject->{DriverLoginResponseInterface::KEY_DATA_RETENTION_DAYS} = $driver->getBranch()->getDataRetentionDays();
        }

        $responseObject->{DriverLoginResponseInterface::KEY_AUTH_VALID} = $response->getAuthValid();
        $responseObject->{DriverLoginResponseInterface::KEY_TOKEN} = $response->getToken();
    }

    /**
     * @param \stdClass $requestObject
     * @return string
     */
    protected function getEmail(stdClass $requestObject): string
    {
        return $requestObject
            ->{DriverLoginRequestInterface::KEY_EMAIL};
    }

    /**
     * @param \stdClass $requestObject
     * @return string
     */
    protected function getPassword(stdClass $requestObject): string
    {
        return $requestObject
            ->{DriverLoginRequestInterface::KEY_PASSWORD};
    }
}
