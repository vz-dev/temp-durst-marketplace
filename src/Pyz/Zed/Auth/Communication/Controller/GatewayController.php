<?php


namespace Pyz\Zed\Auth\Communication\Controller;


use Generated\Shared\Transfer\DriverAppApiRequestTransfer;
use Generated\Shared\Transfer\DriverAppApiResponseTransfer;
use Generated\Shared\Transfer\DriverTransfer;
use Pyz\Zed\Auth\Business\AuthFacadeInterface;
use Pyz\Zed\Auth\Business\Exception\AuthException;
use Pyz\Zed\Driver\Business\Exception\DriverNotExistsException;
use Spryker\Shared\Log\LoggerTrait;
use Spryker\Zed\Kernel\Communication\Controller\AbstractGatewayController;

/**
 * Class GatewayController
 * @package Pyz\Zed\Auth\Communication\Controller
 * @method AuthFacadeInterface getFacade()
 */
class GatewayController extends AbstractGatewayController
{
    use LoggerTrait;

    /**
     * @param \Generated\Shared\Transfer\DriverAppApiRequestTransfer $requestTransfer
     * @return \Generated\Shared\Transfer\DriverAppApiResponseTransfer
     */
    public function driverLoginAction(DriverAppApiRequestTransfer $requestTransfer): DriverAppApiResponseTransfer
    {
        $driverResponse = new DriverAppApiResponseTransfer();

        $driverAuth = false;
        $driverToken = null;

        try {
            $driverToken = $this
                ->getFacade()
                ->driverLogin($requestTransfer->getEmail(), $requestTransfer->getPassword());

            if (is_string($driverToken) === true) {
                $driverAuth = true;
            }

        } catch (AuthException $authException) {
            $this
                ->getLogger()
                ->error($authException->getMessage());
        } catch (DriverNotExistsException $exception){
            $this
                ->getLogger()
                ->error($exception->getMessage());
        }

        $driverResponse
            ->setToken($driverToken)
            ->setAuthValid($driverAuth);

        return $driverResponse;
    }

    /**
     * @param \Generated\Shared\Transfer\DriverAppApiRequestTransfer $requestTransfer
     * @return \Generated\Shared\Transfer\DriverAppApiResponseTransfer
     */
    public function driverLogoutAction(DriverAppApiRequestTransfer $requestTransfer): DriverAppApiResponseTransfer
    {
        $driverResponse = new DriverAppApiResponseTransfer();

        $this
            ->getFacade()
            ->driverLogout($requestTransfer->getToken());

        $driverResponse
            ->setAuthValid(false);

        return $driverResponse;
    }

    /**
     * @param \Generated\Shared\Transfer\DriverAppApiRequestTransfer $requestTransfer
     * @return \Generated\Shared\Transfer\DriverAppApiResponseTransfer
     */
    public function authenticateAction(DriverAppApiRequestTransfer $requestTransfer): DriverAppApiResponseTransfer
    {
        $driverResponse = new DriverAppApiResponseTransfer();

        $driverResponse
            ->setAuthValid(
                $this
                ->getFacade()
                ->isDriverAuthenticated($requestTransfer->getToken())
            );

        return $driverResponse;
    }

    /**
     * @param \Generated\Shared\Transfer\DriverAppApiRequestTransfer $requestTransfer
     * @return \Generated\Shared\Transfer\DriverTransfer
     */
    public function getDriverByTokenAction(DriverAppApiRequestTransfer $requestTransfer): DriverTransfer
    {
        return $this
            ->getFacade()
            ->getDriverByToken($requestTransfer->getToken());
    }
}