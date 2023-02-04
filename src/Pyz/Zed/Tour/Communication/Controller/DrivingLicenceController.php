<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 02.08.18
 * Time: 12:58
 */

namespace Pyz\Zed\Tour\Communication\Controller;


use Generated\Shared\Transfer\DrivingLicenceTransfer;
use Pyz\Zed\Tour\Business\Exception\DrivingLicenceExistsException;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Pyz\Zed\Tour\Business\TourFacadeInterface getFacade()
 * @method \Pyz\Zed\Tour\Communication\TourCommunicationFactory getFactory()
 * @method \Pyz\Zed\Tour\Persistence\TourQueryContainer getQueryContainer()
 */
class DrivingLicenceController extends AbstractController
{
    public const SUCCESS_MESSAGE_DRIVING_LICENCE_CREATED = 'Driving licence with id "%d" created';
    public const SUCCESS_MESSAGE_DRIVING_LICENCE_UPDATED = 'Driving licence with id "%d" updated';
    public const SUCCESS_MESSAGE_DRIVING_LICENCE_DELETED = 'Driving licence with id "%d" deleted';
    public const ERROR_MESSAGE_FAILED_TO_CREATE_DRIVING_LICENCE = 'Failed to create driving licence!';

    public const PARAM_ID_DRIVING_LICENCE = 'id';
    public const DRIVING_LICENCE_LISTING_URL = '/tour/driving-licence/';

    /**
     * @return array
     */
    public function indexAction()
    {
        $table = $this
            ->getFactory()
            ->createDrivingLicenceTable();

        return [
            'drivingLicences' => $table->render(),
        ];
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function tableAction()
    {
        $table = $this
            ->getFactory()
            ->createDrivingLicenceTable();

        return $this
            ->jsonResponse($table->fetchData());
    }

    /**
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws DrivingLicenceExistsException
     */
    public function createAction(Request $request)
    {
        $dataProvider = $this
            ->getFactory()
            ->createDrivingLicenceDataProvider();

        $form = $this
            ->getFactory()
            ->createDrivingLicenceCreateForm($dataProvider->getOptions())
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $drivingLicenceTransfer = $this->getTransferFromForm($form);

            $drivingLicenceWithCodeExists = $this
                ->getFacade()
                ->drivingLicenceWithCodeExists($drivingLicenceTransfer->getCode());

            if ($drivingLicenceWithCodeExists) {
                $this->addErrorMessage(
                    sprintf(
                        DrivingLicenceExistsException::CODE_EXISTS_MESSAGE,
                        $drivingLicenceTransfer->getCode()
                    )
                );
                return $this->viewResponse([
                    'drivingLicenceForm' => $form->createView(),
                ]);
            }

            $drivingLicenceTransfer = $this
                ->getFacade()
                ->addDrivingLicence($drivingLicenceTransfer);

            if ($drivingLicenceTransfer->getIdDrivingLicence()) {
                $this->addSuccessMessage(
                    sprintf(
                        self::SUCCESS_MESSAGE_DRIVING_LICENCE_CREATED,
                        $drivingLicenceTransfer->getIdDrivingLicence()
                    )
                );
                return $this->redirectResponse(self::DRIVING_LICENCE_LISTING_URL);
            }

            $this->addErrorMessage(
                sprintf(
                    self::ERROR_MESSAGE_FAILED_TO_CREATE_DRIVING_LICENCE
                )
            );
        }

        return $this->viewResponse([
            'drivingLicenceForm' => $form->createView(),
        ]);

    }

    /**
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws DrivingLicenceExistsException
     */
    public function editAction(Request $request)
    {
        $idDrivingLicence = $this->castId($request->get(self::PARAM_ID_DRIVING_LICENCE));

        $dataProvider = $this
            ->getFactory()
            ->createDrivingLicenceDataProvider();

        $form = $this
            ->getFactory()
            ->createDrivingLicenceEditForm($dataProvider->getData($idDrivingLicence), $dataProvider->getOptions())
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $drivingLicenceTransfer = $this->getTransferFromForm($form);

            $this
                ->getFacade()
                ->updateDrivingLicence($drivingLicenceTransfer);

            $this->addSuccessMessage(
                sprintf(self::SUCCESS_MESSAGE_DRIVING_LICENCE_UPDATED,
                    $idDrivingLicence
                )
            );
            return $this->redirectResponse(self::DRIVING_LICENCE_LISTING_URL);
        }

        return $this->viewResponse([
            'drivingLicenceForm' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request)
    {
        $idDrivingLicence = $this->castId($request->get(self::PARAM_ID_DRIVING_LICENCE));

        $this
            ->getFacade()
            ->removeDrivingLicenceById($idDrivingLicence);

        $this->addSuccessMessage(
            sprintf(self::SUCCESS_MESSAGE_DRIVING_LICENCE_DELETED,
                $idDrivingLicence
            )
        );
        return $this->redirectResponse(self::DRIVING_LICENCE_LISTING_URL);
    }


    /**
     * @param FormInterface $form
     * @return DrivingLicenceTransfer
     */
    protected function getTransferFromForm(FormInterface $form) : DrivingLicenceTransfer
    {
        return $form->getData();
    }


}
