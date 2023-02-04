<?php


namespace Pyz\Client\Auth;


use Generated\Shared\Transfer\DriverAppApiRequestTransfer;
use Generated\Shared\Transfer\DriverAppApiResponseTransfer;
use Generated\Shared\Transfer\DriverTransfer;

interface AuthClientInterface
{
    /**
     * Identify a driver by email / password combination and get a JWT token
     *
     * @param \Generated\Shared\Transfer\DriverAppApiRequestTransfer $requestTransfer
     * @return \Generated\Shared\Transfer\DriverAppApiResponseTransfer
     */
    public function loginDriver(DriverAppApiRequestTransfer $requestTransfer): DriverAppApiResponseTransfer;

    /**
     * Remove further webservice access for a driver identified by his token
     * 
     * @param \Generated\Shared\Transfer\DriverAppApiRequestTransfer $requestTransfer
     * @return \Generated\Shared\Transfer\DriverAppApiResponseTransfer
     */
    public function logoutDriver(DriverAppApiRequestTransfer $requestTransfer): DriverAppApiResponseTransfer;

    /**
     * Check, if a driver (by his token) is authenticated
     *
     * @param \Generated\Shared\Transfer\DriverAppApiRequestTransfer $requestTransfer
     * @return \Generated\Shared\Transfer\DriverAppApiResponseTransfer
     */
    public function authenticateDriver(DriverAppApiRequestTransfer $requestTransfer): DriverAppApiResponseTransfer;

    /**
     * Get a driver by his token
     *
     * @param \Generated\Shared\Transfer\DriverAppApiRequestTransfer $requestTransfer
     * @return \Generated\Shared\Transfer\DriverTransfer
     */
    public function getDriverByToken(DriverAppApiRequestTransfer $requestTransfer): DriverTransfer;
}