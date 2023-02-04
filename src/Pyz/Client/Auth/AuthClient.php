<?php


namespace Pyz\Client\Auth;


use Generated\Shared\Transfer\DriverAppApiRequestTransfer;
use Generated\Shared\Transfer\DriverAppApiResponseTransfer;
use Generated\Shared\Transfer\DriverTransfer;
use Spryker\Client\Kernel\AbstractClient;

/**
 * Class AuthClient
 * @package Pyz\Client\Auth
 * @method AuthFactory getFactory()
 */
class AuthClient extends AbstractClient implements AuthClientInterface
{

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\DriverAppApiRequestTransfer $requestTransfer
     * @return \Generated\Shared\Transfer\DriverAppApiResponseTransfer
     * @throws \Spryker\Client\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function loginDriver(DriverAppApiRequestTransfer $requestTransfer): DriverAppApiResponseTransfer
    {
        return $this
            ->getFactory()
            ->createAuthStub()
            ->loginDriver($requestTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\DriverAppApiRequestTransfer $requestTransfer
     * @return \Generated\Shared\Transfer\DriverAppApiResponseTransfer
     * @throws \Spryker\Client\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function logoutDriver(DriverAppApiRequestTransfer $requestTransfer): DriverAppApiResponseTransfer
    {
        return $this
            ->getFactory()
            ->createAuthStub()
            ->logoutDriver($requestTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\DriverAppApiRequestTransfer $requestTransfer
     * @return \Generated\Shared\Transfer\DriverAppApiResponseTransfer
     * @throws \Spryker\Client\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function authenticateDriver(DriverAppApiRequestTransfer $requestTransfer): DriverAppApiResponseTransfer
    {
        return $this
            ->getFactory()
            ->createAuthStub()
            ->authenticateDriver($requestTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\DriverAppApiRequestTransfer $requestTransfer
     * @return \Generated\Shared\Transfer\DriverTransfer
     * @throws \Spryker\Client\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getDriverByToken(DriverAppApiRequestTransfer $requestTransfer): DriverTransfer
    {
        return $this
            ->getFactory()
            ->createAuthStub()
            ->getDriverByToken($requestTransfer);
    }
}