<?php
/**
 * Durst - project - SoftwareFeatureController.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 31.10.18
 * Time: 16:55
 */

namespace Pyz\Zed\SoftwarePackage\Communication\Controller;


use Pyz\Zed\SoftwarePackage\Business\SoftwarePackageFacadeInterface;
use Pyz\Zed\SoftwarePackage\Communication\SoftwarePackageCommunicationFactory;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SoftwareFeatureController
 * @package Pyz\Zed\SoftwarePackage\Communication\Controller
 * @method SoftwarePackageFacadeInterface getFacade()
 * @method SoftwarePackageCommunicationFactory getFactory()
 */
class SoftwareFeatureController extends AbstractController
{
    public const URL_LIST = '/software-package/software-feature';
    public const URL_EDIT = '/software-package/software-feature/edit';
    public const URL_DELETE = '/software-package/software-feature/delete';

    public const PARAM_ID_SOFTWARE_FEATURE = 'id-software-feature';

    public const MESSAGE_SUCCESS_UPDATE = 'Software-Feature wurde erfolgreich gespeichert';
    public const MESSAGE_SUCCESS_CREATE = 'Software-Feature wurde erfolgreich hinzugefügt';


    /**
     * @param Request $request
     * @return array
     */
    public function indexAction(Request $request)
    {
        $softwareFeatureTable = $this
            ->getFactory()
            ->createSoftwareFeatureTable();

        return [
            'softwareFeatureTable' => $softwareFeatureTable->render(),
        ];
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function tableAction()
    {
        $softwareFeatureTable = $this
            ->getFactory()
            ->createSoftwareFeatureTable();

        return $this->jsonResponse(
            $softwareFeatureTable->fetchData()
        );
    }

    /**
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function createAction(Request $request)
    {
        $dataProvider = $this
            ->getFactory()
            ->createSoftwareFeatureFormDataProvider();

        $form = $this
            ->getFactory()
            ->createSoftwareFeatureForm(
                $dataProvider->getData(),
                []
            )
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $softwareFeatureTransfer = $form->getData();

            $this
                ->getFacade()
                ->addSoftwareFeature($softwareFeatureTransfer);

            $this->addSuccessMessage(static::MESSAGE_SUCCESS_CREATE);

            return $this->redirectResponse(static::URL_LIST);
        }

        return $this
            ->viewResponse([
                'softwareFeatureForm' => $form->createView(),
            ]);
    }

    /**
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function editAction(Request $request)
    {
        $idSoftwareFeature = $this->castId($request->get(static::PARAM_ID_SOFTWARE_FEATURE));

        if (empty($idSoftwareFeature)) {
            return $this->redirectResponse(static::URL_LIST);
        }

        $dataProvider = $this
            ->getFactory()
            ->createSoftwareFeatureFormDataProvider();

        $softwareFeatureTransfer = $dataProvider->getData($idSoftwareFeature);

        $form = $this
            ->getFactory()
            ->createSoftwareFeatureForm(
                $softwareFeatureTransfer,
                []
            )
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $softwareFeatureTransfer = $form->getData();

            $this
                ->getFacade()
                ->updateSoftwareFeature($softwareFeatureTransfer);

            $this->addSuccessMessage(static::MESSAGE_SUCCESS_UPDATE);

            return $this->redirectResponse(static::URL_LIST);
        }

        return $this
            ->viewResponse([
                'softwareFeatureForm' => $form->createView(),
                'code' => $softwareFeatureTransfer->getCode(),
            ]);
    }
}