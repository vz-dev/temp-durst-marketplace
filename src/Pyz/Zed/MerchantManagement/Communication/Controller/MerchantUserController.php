<?php
/**
 * Durst - project - MerchantUserController.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 01.04.21
 * Time: 14:59
 */

namespace Pyz\Zed\MerchantManagement\Communication\Controller;

use Exception;
use Orm\Zed\Merchant\Persistence\Map\DstMerchantUserTableMap;
use Pyz\Zed\Merchant\Business\Exception\MerchantUserException;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class MerchantUserController
 * @package Pyz\Zed\MerchantManagement\Communication\Controller
 * @method \Pyz\Zed\MerchantManagement\Communication\MerchantManagementCommunicationFactory getFactory()
 */
class MerchantUserController extends AbstractController
{
    public const MERCHANT_USER_LISTING_URL = '/merchant-management/merchant-user';
    public const UPDATE_MERCHANT_USER_URL = '/merchant-management/merchant-user/update';
    public const DELETE_MERCHANT_USER_URL = '/merchant-management/merchant-user/delete';
    public const RESTORE_MERCHANT_USER_URL = '/merchant-management/merchant-user/restore';

    public const PARAM_ID_MERCHANT_USER = 'id-merchant-user';

    protected const MESSAGE_MERCHANT_USER_CREATE_SUCCESS = 'Der Benutzer %s wurde erfolgreich angelegt.';
    protected const MESSAGE_MERCHANT_USER_CREATE_ERROR = 'Der Benutzer konnte nicht angelegt werden.';

    protected const MESSAGE_MERCHANT_USER_UPDATE_SUCCESS = 'Der Benutzer %s wurde erfolgreich geändert.';

    protected const MESSAGE_MERCHANT_USER_STATUS_ERROR = 'Der Status des Benutzers mit der ID %d konnte nicht geändert werden.';

    protected const MESSAGE_MISSING_ID_PARAMETER = 'Es wurde keine ID übergeben.';

    protected const MESSAGE_MERCHANT_USER_STATUS = 'Der Benutzer mit der ID %d wurde erfolgreich %s.';

    protected const MERCHANT_USER_STATUSES = [
        DstMerchantUserTableMap::COL_STATUS_ACTIVE => 'aktiviert',
        DstMerchantUserTableMap::COL_STATUS_BLOCKED => 'deaktiviert',
        DstMerchantUserTableMap::COL_STATUS_DELETED => 'gelöscht'
    ];

    /**
     * @return array
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function indexAction(): array
    {
        $merchantUsersTable = $this
            ->getFactory()
            ->createTableFactory()
            ->createMerchantUserTable();

        return $this
            ->viewResponse(
                [
                    'merchantUsers' => $merchantUsersTable->render()
                ]
            );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function tableAction(): JsonResponse
    {
        $merchantUsersTable = $this
            ->getFactory()
            ->createTableFactory()
            ->createMerchantUserTable();

        return $this
            ->jsonResponse(
                $merchantUsersTable->fetchData()
            );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createAction(Request $request)
    {
        $dataProvider = $this
            ->getFactory()
            ->createFormFactory()
            ->createMerchantUserFormDataProvider();

        $form = $this
            ->getFactory()
            ->createFormFactory()
            ->createMerchantUserForm(
                $dataProvider->getData(null),
                $dataProvider->getOptions()
            )
            ->handleRequest(
                $request
            );

        if ($form->isSubmitted() === true) {
            if ($form->isValid() === true) {
                $merchantUserTransfer = $form
                    ->getData();

                try {
                    $merchantUserTransfer = $this
                        ->getFactory()
                        ->getMerchantFacade()
                        ->createMerchantUser($merchantUserTransfer);

                    $this
                        ->addSuccessMessage(
                            sprintf(
                                static::MESSAGE_MERCHANT_USER_CREATE_SUCCESS,
                                $merchantUserTransfer->getEmail()
                            )
                        );

                    return $this
                        ->redirectResponse(
                            static::MERCHANT_USER_LISTING_URL
                        );

                } catch (MerchantUserException $merchantUserException) {
                    $this
                        ->addErrorMessage(
                            $merchantUserException->getMessage()
                        );
                }
            } else {
                $this
                    ->addErrorMessage(
                        static::MESSAGE_MERCHANT_USER_CREATE_ERROR
                    );
            }
        }

        return $this
            ->viewResponse([
                'merchantUserForm' => $form->createView(),
            ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function updateAction(Request $request)
    {
        $idMerchantUser = $this
            ->castId(
                $request->get(static::PARAM_ID_MERCHANT_USER)
            );

        if ($idMerchantUser === null || empty($idMerchantUser) === true) {
            $this
                ->addErrorMessage(
                    static::MESSAGE_MISSING_ID_PARAMETER
                );

            return $this
                ->redirectResponse(
                    static::MERCHANT_USER_LISTING_URL
                );
        }

        $dataProvider = $this
            ->getFactory()
            ->createFormFactory()
            ->createMerchantUserFormDataProvider();

        $merchantUserForm = $this
            ->getFactory()
            ->createFormFactory()
            ->createMerchantUserForm(
                $dataProvider->getData($idMerchantUser),
                $dataProvider->getOptions()
            )
            ->handleRequest(
                $request
            );

        if ($merchantUserForm->isSubmitted() && $merchantUserForm->isValid()) {
            $merchantUserTransfer = $merchantUserForm
                ->getData();

            try {
                $merchantUserTransfer = $this
                    ->getFactory()
                    ->getMerchantFacade()
                    ->createMerchantUser($merchantUserTransfer);

                $this
                    ->addSuccessMessage(
                        sprintf(
                            static::MESSAGE_MERCHANT_USER_UPDATE_SUCCESS,
                            $merchantUserTransfer->getEmail()
                        )
                    );

                return $this
                    ->redirectResponse(
                        static::MERCHANT_USER_LISTING_URL
                    );

            } catch (Exception $exception) {
                $this
                    ->addErrorMessage(
                        $exception->getMessage()
                    );
            }
        }

        return $this
            ->viewResponse(
                [
                    'idMerchantUser' => $idMerchantUser,
                    'merchantUserForm' => $merchantUserForm->createView()
                ]
            );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function deleteAction(Request $request): RedirectResponse
    {
        $idMerchantUser = $this
            ->castId(
                $request->get(static::PARAM_ID_MERCHANT_USER)
            );

        if ($idMerchantUser === null || empty($idMerchantUser) === true) {
            $this
                ->addErrorMessage(
                    static::MESSAGE_MISSING_ID_PARAMETER
                );

            return $this
                ->redirectResponse(
                    static::MERCHANT_USER_LISTING_URL
                );
        }

        try {
            $updatedStatus = $this
                ->getFactory()
                ->getMerchantFacade()
                ->deleteMerchantUser($idMerchantUser);

            if ($updatedStatus === true) {
                $this
                    ->addSuccessMessage(
                        sprintf(
                            static::MESSAGE_MERCHANT_USER_STATUS,
                            $idMerchantUser,
                            static::MERCHANT_USER_STATUSES[DstMerchantUserTableMap::COL_STATUS_DELETED]
                        )
                    );
            } else {
                $this
                    ->addErrorMessage(
                        sprintf(
                            static::MESSAGE_MERCHANT_USER_STATUS_ERROR,
                            $idMerchantUser
                        )
                    );
            }
        } catch (MerchantUserException $merchantUserException) {
            $this
                ->addErrorMessage(
                    $merchantUserException->getMessage()
                );
        }

        return $this
            ->redirectResponse(
                static::MERCHANT_USER_LISTING_URL
            );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function restoreAction(Request $request): RedirectResponse
    {
        $idMerchantUser = $this
            ->castId(
                $request->get(static::PARAM_ID_MERCHANT_USER)
            );

        if ($idMerchantUser === null || empty($idMerchantUser) === true) {
            $this
                ->addErrorMessage(
                    static::MESSAGE_MISSING_ID_PARAMETER
                );

            return $this
                ->redirectResponse(
                    static::MERCHANT_USER_LISTING_URL
                );
        }

        try {
            $updatedStatus = $this
                ->getFactory()
                ->getMerchantFacade()
                ->activateMerchantUser($idMerchantUser);

            if ($updatedStatus === true) {
                $this
                    ->addSuccessMessage(
                        sprintf(
                            static::MESSAGE_MERCHANT_USER_STATUS,
                            $idMerchantUser,
                            static::MERCHANT_USER_STATUSES[DstMerchantUserTableMap::COL_STATUS_ACTIVE]
                        )
                    );
            } else {
                $this
                    ->addErrorMessage(
                        sprintf(
                            static::MESSAGE_MERCHANT_USER_STATUS_ERROR,
                            $idMerchantUser
                        )
                    );
            }
        } catch (MerchantUserException $merchantUserException) {
            $this
                ->addErrorMessage(
                    $merchantUserException->getMessage()
                );
        }

        return $this
            ->redirectResponse(
                static::MERCHANT_USER_LISTING_URL
            );
    }
}
