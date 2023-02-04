<?php


namespace Pyz\Yves\AppRestApi\Handler\Hydrator\DriverApp;


use Generated\Shared\Transfer\DriverAppApiRequestTransfer;
use Pyz\Client\Auth\AuthClientInterface;
use Pyz\Yves\AppRestApi\Handler\Hydrator\HydratorInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Request\DriverLogoutRequestInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Response\DriverLogoutResponseInterface;
use stdClass;

class LogoutHydrator implements HydratorInterface
{
    /**
     * @var \Pyz\Client\Auth\AuthClientInterface
     */
    protected $authClient;

    /**
     * LogoutHydrator constructor.
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
    public function hydrate(stdClass $requestObject, stdClass $responseObject, string $version = 'v1'): void
    {
        $requestTransfer = (new DriverAppApiRequestTransfer())
            ->setToken($this->getToken($requestObject));

        $response = $this
            ->authClient
            ->logoutDriver($requestTransfer);

        $responseObject->{DriverLogoutResponseInterface::KEY_AUTH_VALID} = $response->getAuthValid();
    }

    /**
     * @param \stdClass $requestObject
     * @return string
     */
    protected function getToken(stdClass $requestObject): string
    {
        return $requestObject
            ->{DriverLogoutRequestInterface::KEY_TOKEN};
    }
}
