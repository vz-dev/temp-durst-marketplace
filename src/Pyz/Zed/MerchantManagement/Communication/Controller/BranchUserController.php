<?php
/**
 * Durst - project - BranchUserController.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 05.02.20
 * Time: 15:03
 */

namespace Pyz\Zed\MerchantManagement\Communication\Controller;

use Exception;
use Orm\Zed\Merchant\Persistence\Map\DstBranchUserTableMap;
use Pyz\Zed\Merchant\Business\Exception\BranchUserException;
use Pyz\Zed\MerchantManagement\Communication\MerchantManagementCommunicationFactory;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BranchUserController
 * @package Pyz\Zed\MerchantManagement\Communication\Controller
 * @method MerchantManagementCommunicationFactory getFactory()
 */
class BranchUserController extends AbstractController
{
    public const BRANCH_USER_LISTING_URL = '/merchant-management/branch-user';
    public const UPDATE_BRANCH_USER_URL = '/merchant-management/branch-user/update';
    public const DELETE_BRANCH_USER_URL = '/merchant-management/branch-user/delete';
    public const RESTORE_BRANCH_USER_URL = '/merchant-management/branch-user/restore';

    public const PARAM_ID_BRANCH_USER = 'id-branch-user';

    protected const MESSAGE_BRANCH_USER_CREATE_SUCCESS = 'Der Benutzer %s wurde erfolgreich angelegt.';
    protected const MESSAGE_BRANCH_USER_CREATE_ERROR = 'Der Benutzer konnte nicht angelegt werden.';

    protected const MESSAGE_BRANCH_USER_UPDATE_SUCCESS = 'Der Benutzer %s wurde erfolgreich geändert.';

    protected const MESSAGE_BRANCH_USER_STATUS_ERROR = 'Der Status des Benutzers mit der ID %d konnte nicht geändert werden.';

    protected const MESSAGE_MISSING_ID_PARAMETER = 'Es wurde keine ID übergeben.';

    protected const MESSAGE_BRANCH_USER_STATUS = 'Der Benutzer mit der ID %d wurde erfolgreich %s.';

    protected const BRANCH_USER_STATUSES = [
        DstBranchUserTableMap::COL_STATUS_ACTIVE => 'aktiviert',
        DstBranchUserTableMap::COL_STATUS_BLOCKED => 'deaktiviert',
        DstBranchUserTableMap::COL_STATUS_DELETED => 'gelöscht'
    ];

    /**
     * @return array
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function indexAction(): array
    {
        $branchUsersTable = $this
            ->getFactory()
            ->createTableFactory()
            ->createBranchUserTable();

        return $this
            ->viewResponse([
                'branchUsers' => $branchUsersTable->render()
            ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function tableAction(): JsonResponse
    {
        $branchUsersTable = $this
            ->getFactory()
            ->createTableFactory()
            ->createBranchUserTable();

        return $this
            ->jsonResponse(
                $branchUsersTable->fetchData()
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
            ->createBranchUserFormDataProvider();

        $form = $this
            ->getFactory()
            ->createFormFactory()
            ->createBranchUserForm(
                $dataProvider->getData(null),
                $dataProvider->getOptions()
            )
            ->handleRequest(
                $request
            );

        if ($form->isSubmitted() === true) {
            if ($form->isValid() === true) {
                $branchUserTransfer = $form
                    ->getData();

                try {
                    $branchUserTransfer = $this
                        ->getFactory()
                        ->getMerchantFacade()
                        ->createBranchUser($branchUserTransfer);

                    $this
                        ->addSuccessMessage(
                            sprintf(
                                static::MESSAGE_BRANCH_USER_CREATE_SUCCESS,
                                $branchUserTransfer->getEmail()
                            )
                        );

                    return $this
                        ->redirectResponse(
                            static::BRANCH_USER_LISTING_URL
                        );

                } catch (BranchUserException $branchUserException) {
                    $this
                        ->addErrorMessage(
                            $branchUserException->getMessage()
                        );
                }
            } else {
                $this
                    ->addErrorMessage(
                        static::MESSAGE_BRANCH_USER_CREATE_ERROR
                    );
            }
        }

        return $this
            ->viewResponse([
                'branchUserForm' => $form->createView(),
            ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function updateAction(Request $request)
    {
        $idBranchUser = $this
            ->castId(
                $request->get(static::PARAM_ID_BRANCH_USER)
            );

        if ($idBranchUser === null || empty($idBranchUser) === true) {
            $this
                ->addErrorMessage(
                    static::MESSAGE_MISSING_ID_PARAMETER
                );

            return $this
                ->redirectResponse(
                    static::BRANCH_USER_LISTING_URL
                );
        }

        $dataProvider = $this
            ->getFactory()
            ->createFormFactory()
            ->createBranchUserFormDataProvider();

        $branchUserForm = $this
            ->getFactory()
            ->createFormFactory()
            ->createBranchUserForm(
                $dataProvider->getData($idBranchUser),
                $dataProvider->getOptions()
            )
            ->handleRequest(
                $request
            );

        if ($branchUserForm->isSubmitted() && $branchUserForm->isValid()) {
            $branchUserTransfer = $branchUserForm
                ->getData();

            try {
                $branchUserTransfer = $this
                    ->getFactory()
                    ->getMerchantFacade()
                    ->createBranchUser($branchUserTransfer);

                $this
                    ->addSuccessMessage(
                        sprintf(
                            static::MESSAGE_BRANCH_USER_UPDATE_SUCCESS,
                            $branchUserTransfer->getEmail()
                        )
                    );

                return $this
                    ->redirectResponse(
                        static::BRANCH_USER_LISTING_URL
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
                    'idBranchUser' => $idBranchUser,
                    'branchUserForm' => $branchUserForm->createView()
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
        $idBranchUser = $this
            ->castId(
                $request->get(static::PARAM_ID_BRANCH_USER)
            );

        if ($idBranchUser === null || empty($idBranchUser) === true) {
            $this
                ->addErrorMessage(
                    static::MESSAGE_MISSING_ID_PARAMETER
                );

            return $this
                ->redirectResponse(
                    static::BRANCH_USER_LISTING_URL
                );
        }

        try {
            $updatedStatus = $this
                ->getFactory()
                ->getMerchantFacade()
                ->deleteBranchUser($idBranchUser);

            if ($updatedStatus === true) {
                $this
                    ->addSuccessMessage(
                        sprintf(
                            static::MESSAGE_BRANCH_USER_STATUS,
                            $idBranchUser,
                            static::BRANCH_USER_STATUSES[DstBranchUserTableMap::COL_STATUS_DELETED]
                        )
                    );
            } else {
                $this
                    ->addErrorMessage(
                        sprintf(
                            static::MESSAGE_BRANCH_USER_STATUS_ERROR,
                            $idBranchUser
                        )
                    );
            }
        } catch (BranchUserException $branchUserException) {
            $this
                ->addErrorMessage(
                    $branchUserException->getMessage()
                );
        }

        return $this
            ->redirectResponse(
                static::BRANCH_USER_LISTING_URL
            );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function restoreAction(Request $request): RedirectResponse
    {
        $idBranchUser = $this
            ->castId(
                $request->get(static::PARAM_ID_BRANCH_USER)
            );

        if ($idBranchUser === null || empty($idBranchUser) === true) {
            $this
                ->addErrorMessage(
                    static::MESSAGE_MISSING_ID_PARAMETER
                );

            return $this
                ->redirectResponse(
                    static::BRANCH_USER_LISTING_URL
                );
        }

        try {
            $updatedStatus = $this
                ->getFactory()
                ->getMerchantFacade()
                ->activateBranchUser($idBranchUser);

            if ($updatedStatus === true) {
                $this
                    ->addSuccessMessage(
                        sprintf(
                            static::MESSAGE_BRANCH_USER_STATUS,
                            $idBranchUser,
                            static::BRANCH_USER_STATUSES[DstBranchUserTableMap::COL_STATUS_ACTIVE]
                        )
                    );
            } else {
                $this
                    ->addErrorMessage(
                        sprintf(
                            static::MESSAGE_BRANCH_USER_STATUS_ERROR,
                            $idBranchUser
                        )
                    );
            }
        } catch (BranchUserException $branchUserException) {
            $this
                ->addErrorMessage(
                    $branchUserException->getMessage()
                );
        }

        return $this
            ->redirectResponse(
                static::BRANCH_USER_LISTING_URL
            );
    }
}
