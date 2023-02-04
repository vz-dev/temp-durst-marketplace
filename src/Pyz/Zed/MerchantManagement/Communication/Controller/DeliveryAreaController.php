<?php

namespace Pyz\Zed\MerchantManagement\Communication\Controller;

use Generated\Shared\Transfer\DeliveryAreaTransfer;
use Pyz\Zed\MerchantManagement\Communication\Form\DeliveryAreaForm;
use Pyz\Zed\MerchantManagement\MerchantManagementConfig;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

/**
 * @method \Pyz\Zed\MerchantManagement\Communication\MerchantManagementCommunicationFactory getFactory()
 */
class DeliveryAreaController extends AbstractController
{

    /**
     * @return array
     */
    public function indexAction()
    {
        $table = $this
            ->getFactory()
            ->createTableFactory()
            ->createDeliveryAreaTable();

        return [
            'deliveryAreas' => $table->render(),
        ];
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function tableAction()
    {
        $table = $this
            ->getFactory()
            ->createTableFactory()
            ->createDeliveryAreaTable();

        return $this->jsonResponse(
            $table->fetchData()
        );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function createAction(Request $request)
    {
        $dataProvider = $this
            ->getFactory()
            ->createFormFactory()
            ->createDeliveryAreaFormDataProvider();

        $deliveryAreaForm = $this->getFactory()
            ->createFormFactory()
            ->createDeliveryAreaForm(
                [],
                $dataProvider->getOptions()
            )
            ->handleRequest($request);

        $viewData = [
            'deliveryAreaForm' => $deliveryAreaForm->createView(),
        ];

        if ($deliveryAreaForm->isValid()) {
            $formData = $deliveryAreaForm->getData();

            $deliveryAreaTransfer = $this
                ->getFactory()
                ->getDeliveryAreaFacade()
                ->addDeliveryArea(
                    $formData[DeliveryAreaForm::FIELD_NAME],
                    $formData[DeliveryAreaForm::FIELD_CITY],
                    $formData[DeliveryAreaForm::FIELD_ZIP]
                );

            if ($deliveryAreaTransfer->getIdDeliveryArea()) {

                $this->addSuccessMessage(
                    sprintf('Delivery area with id "%d" created', $deliveryAreaTransfer->getIdDeliveryArea())
                );

                return $this->redirectResponse(MerchantManagementConfig::DELIVERY_AREA_LISTING_URL);
            }

            $this->addErrorMessage('Failed to create new delivery area!');
        }

        return $this->viewResponse($viewData);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function updateAction(Request $request)
    {
        $idDeliveryArea = $this->castId($request->get(MerchantManagementConfig::PARAM_ID_DELIVERY_AREA));

        if (empty($idDeliveryArea)) {
            $this->addErrorMessage('Missing delivery area id!');

            return $this->redirectResponse(MerchantManagementConfig::DELIVERY_AREA_LISTING_URL);
        }

        $dataProvider = $this
            ->getFactory()
            ->createFormFactory()
            ->createDeliveryAreaFormDataProvider();

        $deliveryAreaForm = $this->getFactory()
            ->createFormFactory()
            ->createDeliveryAreaForm(
                $dataProvider->getData($idDeliveryArea),
                $dataProvider->getOptions()
            )
            ->handleRequest($request);

        if ($deliveryAreaForm->isValid()) {
            $formData = $deliveryAreaForm->getData();
            $deliveryAreaTransfer = new DeliveryAreaTransfer();
            $deliveryAreaTransfer->fromArray($formData, true);
            $deliveryAreaTransfer->setIdDeliveryArea($idDeliveryArea);
            $this
                ->getFactory()
                ->getDeliveryAreaFacade()
                ->updateDeliveryArea($deliveryAreaTransfer);

            $this->addSuccessMessage('Delivery area updated.');

            return $this->redirectResponse(MerchantManagementConfig::DELIVERY_AREA_LISTING_URL);
        }

        return $this->viewResponse([
            'deliveryAreaForm' => $deliveryAreaForm->createView(),
            'idDeliveryArea' => $idDeliveryArea,
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request)
    {
        if (!$request->isMethod(Request::METHOD_DELETE)) {
            throw new MethodNotAllowedHttpException([Request::METHOD_DELETE], 'This action requires a DELETE request.');
        }

        $idDeliveryArea = $this->castId($request->request->get(MerchantManagementConfig::PARAM_ID_DELIVERY_AREA));

        if (empty($idDeliveryArea)) {
            $this->addErrorMessage('Missing delivery area id!');

            return $this->redirectResponse(MerchantManagementConfig::DELIVERY_AREA_LISTING_URL);
        }

        $this
            ->getFactory()
            ->getDeliveryAreaFacade()
            ->removeDeliveryArea($idDeliveryArea);

        if($this
            ->getFactory()
            ->getDeliveryAreaFacade()
            ->hasDeliveryArea($idDeliveryArea)
        ) {
            $this->addErrorMessage(sprintf('Failed to delete delivery area with id "%d".', $idDeliveryArea));
        } else {
            $this->addSuccessMessage(sprintf('Delivery area with id "%d" successfully deleted.', $idDeliveryArea));
        }

        return $this->redirectResponse(MerchantManagementConfig::DELIVERY_AREA_LISTING_URL);
    }
}
