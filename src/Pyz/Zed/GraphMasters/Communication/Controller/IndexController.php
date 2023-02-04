<?php

namespace Pyz\Zed\GraphMasters\Communication\Controller;

use Exception;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Pyz\Zed\GraphMasters\Business\GraphMastersFacade getFacade()
 * @method \Pyz\Zed\GraphMasters\Communication\GraphMastersCommunicationFactory getFactory()
 * @method \Pyz\Zed\GraphMasters\Persistence\GraphMastersQueryContainer getQueryContainer()
 */
class IndexController extends AbstractController
{
    public const PARAM_ID_SETTINGS = 'id-settings';

    public const URL_EDIT = '/graph-masters/index/edit';
    public const URL_REMOVE = '/graph-masters/index/remove';
    public const URL_INDEX = '/graph-masters';

    /**
     * @return array
     */
    public function indexAction()
    {
        $table = $this
            ->getFactory()
            ->createSettingsTable();

        return [
            'table' => $table->render(),
        ];
    }

    /**
     * @return JsonResponse
     */
    public function tableAction()
    {
        $table = $this
            ->getFactory()
            ->createSettingsTable();

        return $this->jsonResponse(
            $table->fetchData()
        );
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function removeAction(Request $request)
    {
        $idSettings = $this->castId($request->get(static::PARAM_ID_SETTINGS, -1));

        $this
            ->getFacade()
            ->removeSettings($idSettings);

        $this->addSuccessMessage(
            sprintf(
                'Settings #%d erfolgreich gelöscht',
                $idSettings
            )
        );

        return $this->redirectResponse(static::URL_INDEX);
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function addAction(Request $request)
    {
        return $this->handleSaveForm($request);
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function editAction(Request $request)
    {
        $idSettings = $this->castId($request->get(static::PARAM_ID_SETTINGS, -1));
        if ($idSettings === -1) {
            $idSettings = null;
        }

        return $this->handleSaveForm($request, $idSettings);
    }

    /**
     * @param Request $request
     * @param int|null $idSettings
     *
     * @return mixed
     */
    protected function handleSaveForm(Request $request, ?int $idSettings = null)
    {
        $dataProvider = $this
            ->getFactory()
            ->createSettingsFormDataProvider();

        $form = $this
            ->getFactory()
            ->createSettingsForm(
                $dataProvider->getData($idSettings),
                $dataProvider->getOptions($idSettings)
            )
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this
                    ->getFacade()
                    ->saveSetting($form->getData());

                $this->addSuccessMessage('settings saved successfully');
            } catch (Exception $e) {
                $this->addErrorMessage($e->getMessage());
            }

            return $this->redirectResponse(static::URL_INDEX);
        }

        return $this->viewResponse([
            'form' => $form->createView(),
        ]);
    }

}
