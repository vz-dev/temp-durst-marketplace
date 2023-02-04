<?php


namespace Pyz\Client\Auth\Zed;


use Generated\Shared\Transfer\DriverAppApiRequestTransfer;
use Generated\Shared\Transfer\DriverAppApiResponseTransfer;
use Generated\Shared\Transfer\DriverTransfer;

interface AuthStubInterface
{

    /**
     * @param \Generated\Shared\Transfer\DriverAppApiRequestTransfer $requestTransfer
     * @return \Generated\Shared\Transfer\DriverAppApiResponseTransfer
     */
    public function loginDriver(DriverAppApiRequestTransfer $requestTransfer): DriverAppApiResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\DriverAppApiRequestTransfer $requestTransfer
     * @return \Generated\Shared\Transfer\DriverAppApiResponseTransfer
     */
    public function logoutDriver(DriverAppApiRequestTransfer $requestTransfer): DriverAppApiResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\DriverAppApiRequestTransfer $requestTransfer
     * @return \Generated\Shared\Transfer\DriverAppApiResponseTransfer
     */
    public function authenticateDriver(DriverAppApiRequestTransfer $requestTransfer): DriverAppApiResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\DriverAppApiRequestTransfer $requestTransfer
     * @return \Generated\Shared\Transfer\DriverTransfer
     */
    public function getDriverByToken(DriverAppApiRequestTransfer $requestTransfer): DriverTransfer;
}