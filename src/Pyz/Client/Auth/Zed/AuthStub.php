<?php


namespace Pyz\Client\Auth\Zed;


use Generated\Shared\Transfer\DriverAppApiRequestTransfer;
use Generated\Shared\Transfer\DriverAppApiResponseTransfer;
use Generated\Shared\Transfer\DriverTransfer;
use Spryker\Client\ZedRequest\ZedRequestClientInterface;

class AuthStub implements AuthStubInterface
{
    protected const URL_DRIVER_APP_LOGIN = '/auth/gateway/driver-login';
    protected const URL_DRIVER_APP_LOGOUT = '/auth/gateway/driver-logout';
    protected const URL_DRIVER_APP_AUTHENTICATE_DRIVER = '/auth/gateway/authenticate';
    protected const URL_DRIVER_APP_GET_DRIVER_BY_TOKEN = '/auth/gateway/get-driver-by-token';

    /**
     * @var \Spryker\Client\ZedRequest\ZedRequestClientInterface
     */
    protected $zedStub;

    /**
     * AuthStub constructor.
     * @param \Spryker\Client\ZedRequest\ZedRequestClientInterface $zedStub
     */
    public function __construct(ZedRequestClientInterface $zedStub)
    {
        $this->zedStub = $zedStub;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\DriverAppApiRequestTransfer $requestTransfer
     * @return \Generated\Shared\Transfer\DriverAppApiResponseTransfer|\Spryker\Shared\Kernel\Transfer\TransferInterface
     */
    public function loginDriver(DriverAppApiRequestTransfer $requestTransfer): DriverAppApiResponseTransfer
    {
        return $this
            ->zedStub
            ->call(
                self::URL_DRIVER_APP_LOGIN,
                $requestTransfer,
                null
            );
    }

    /**
     * @param \Generated\Shared\Transfer\DriverAppApiRequestTransfer $requestTransfer
     * @return \Generated\Shared\Transfer\DriverAppApiResponseTransfer|\Spryker\Shared\Kernel\Transfer\TransferInterface
     */
    public function logoutDriver(DriverAppApiRequestTransfer $requestTransfer): DriverAppApiResponseTransfer
    {
        return $this
            ->zedStub
            ->call(
                self::URL_DRIVER_APP_LOGOUT,
                $requestTransfer,
                null
            );
    }

    /**
     * @param \Generated\Shared\Transfer\DriverAppApiRequestTransfer $requestTransfer
     * @return \Generated\Shared\Transfer\DriverAppApiResponseTransfer|\Spryker\Shared\Kernel\Transfer\TransferInterface
     */
    public function authenticateDriver(DriverAppApiRequestTransfer $requestTransfer): DriverAppApiResponseTransfer
    {
        return $this
            ->zedStub
            ->call(
                self::URL_DRIVER_APP_AUTHENTICATE_DRIVER,
                $requestTransfer,
                null
            );
    }

    /**
     * @param \Generated\Shared\Transfer\DriverAppApiRequestTransfer $requestTransfer
     * @return \Generated\Shared\Transfer\DriverTransfer|\Spryker\Shared\Kernel\Transfer\TransferInterface
     */
    public function getDriverByToken(DriverAppApiRequestTransfer $requestTransfer): DriverTransfer
    {
        return $this
            ->zedStub
            ->call(
                self::URL_DRIVER_APP_GET_DRIVER_BY_TOKEN,
                $requestTransfer,
                null
            );
    }
}