<?php
/**
 * Durst - project - EditController.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 26.07.18
 * Time: 15:16
 */

namespace Pyz\Zed\SoftwarePackage\Communication\Controller;

use Generated\Shared\Transfer\SoftwarePackageTransfer;
use Pyz\Zed\SoftwarePackage\Business\SoftwarePackageFacadeInterface;
use Pyz\Zed\SoftwarePackage\Communication\SoftwarePackageCommunicationFactory;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class EditController
 * @package Pyz\Zed\SoftwarePackage\Communication\Controller
 * @method SoftwarePackageFacadeInterface getFacade()
 * @method SoftwarePackageCommunicationFactory getFactory()
 */
class EditController extends AbstractController
{
    public const URL_LIST = '/software-package';
    public const URL_UPDATE = '/software-package/edit/update';
    public const URL_DELETE = '/software-package/edit/delete';
    public const URL_ACTIVATE = '/software-package/edit/activate';
    public const URL_DEACTIVATE = '/software-package/edit/deactivate';

    public const PARAM_ID_SOFTWARE_PACKAGE = 'id-software-package';

    public const MESSAGE_SUCCESS_UPDATE = 'Software-Paket wurde erfolgreich gespeichert';
    public const MESSAGE_SUCCESS_CREATE = 'Software-Paket wurde erfolgreich hinzugefügt';

    /**
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function updateAction(Request $request)
    {
        $idSoftwarePackage = $this->castId($request->get(static::PARAM_ID_SOFTWARE_PACKAGE));

        if (empty($idSoftwarePackage)) {
            return $this->redirectResponse(static::URL_LIST);
        }

        $dataProvider = $this
            ->getFactory()
            ->createSoftwarePackageFormDataProvider();

        $softwarePackageTransfer = $dataProvider->getData($idSoftwarePackage);

        $form = $this
            ->getFactory()
            ->createSoftwarePackageForm(
                $softwarePackageTransfer,
                $dataProvider->getOptions()
            )
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $softwarePackageTransfer = $this->getDataFromForm($form);

            $this
                ->getFacade()
                ->updateSoftwarePackage($softwarePackageTransfer);

            $this->addSuccessMessage(static::MESSAGE_SUCCESS_UPDATE);

            return $this->redirectResponse(static::URL_LIST);
        }

        return $this
            ->viewResponse([
                'softwarePackageForm' => $form->createView(),
                'code' => $softwarePackageTransfer->getCode(),
            ]);
    }

    /**
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createAction(Request $request)
    {
        $dataProvider = $this
            ->getFactory()
            ->createSoftwarePackageFormDataProvider();

        $form = $this
            ->getFactory()
            ->createSoftwarePackageForm(
                $dataProvider->getData(),
                $dataProvider->getOptions()
            )
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $softwarePackageTransfer = $this->getDataFromForm($form);

            $this
                ->getFacade()
                ->updateSoftwarePackage($softwarePackageTransfer);

            $this->addSuccessMessage(static::MESSAGE_SUCCESS_CREATE);

            return $this->redirectResponse(static::URL_LIST);
        }

        return $this
            ->viewResponse([
                'softwarePackageForm' => $form->createView(),
            ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function activateAction(Request $request)
    {
        $idSoftwarePackage = $this->castId($request->get(static::PARAM_ID_SOFTWARE_PACKAGE));

        if (empty($idSoftwarePackage)) {
            return $this->redirectResponse(static::URL_LIST);
        }

        $this
            ->getFacade()
            ->activateSoftwarePackage($idSoftwarePackage);

        return $this->redirectResponse(static::URL_LIST);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deactivateAction(Request $request)
    {
        $idSoftwarePackage = $this->castId($request->get(static::PARAM_ID_SOFTWARE_PACKAGE));

        if (empty($idSoftwarePackage)) {
            return $this->redirectResponse(static::URL_LIST);
        }

        $this
            ->getFacade()
            ->deactivateSoftwarePackage($idSoftwarePackage);

        return $this->redirectResponse(static::URL_LIST);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request)
    {
        $idSoftwarePackage = $this->castId($request->get(static::PARAM_ID_SOFTWARE_PACKAGE));

        if (empty($idSoftwarePackage)) {
            return $this->redirectResponse(static::URL_LIST);
        }

        $this
            ->getFacade()
            ->deleteSoftwarePackage($idSoftwarePackage);

        return $this->redirectResponse(static::URL_LIST);
    }

    /**
     * @param FormInterface $form
     * @return SoftwarePackageTransfer
     */
    protected function getDataFromForm(FormInterface $form) : SoftwarePackageTransfer
    {
        return $form->getData();
    }
}