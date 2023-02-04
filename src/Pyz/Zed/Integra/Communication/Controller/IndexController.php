<?php

namespace Pyz\Zed\Integra\Communication\Controller;

use Exception;
use Pyz\Zed\Integra\Business\IntegraFacade;
use Pyz\Zed\Integra\Communication\IntegraCommunicationFactory;
use Pyz\Zed\Integra\Persistence\IntegraQueryContainer;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method IntegraFacade getFacade()
 * @method IntegraCommunicationFactory getFactory()
 * @method IntegraQueryContainer getQueryContainer()
 */
class IndexController extends AbstractController
{
    public const PARAM_ID_CREDENTIALS = 'id-cred';

    public const URL_EDIT = '/integra/index/edit';
    public const URL_REMOVE = '/integra/index/remove';
    public const URL_INDEX = '/integra';

    /**
     * @return array
     */
    public function indexAction()
    {
        $table = $this
            ->getFactory()
            ->createCredentialsTable();

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
            ->createCredentialsTable();

        return $this->jsonResponse(
            $table->fetchData()
        );
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
        $idCred = $this->castId($request->get(static::PARAM_ID_CREDENTIALS, -1));
        if ($idCred === -1) {
            $idCred = null;
        }

        return $this->handleSaveForm($request, $idCred);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function removeAction(Request $request)
    {
        $idCred = $this->castId($request->get(static::PARAM_ID_CREDENTIALS, -1));

        $this
            ->getFacade()
            ->removeCredentials($idCred);

        $this->addSuccessMessage(
            sprintf(
                'Konfiguration #%d erfolgreich gelöscht',
                $idCred
            )
        );

        return $this->redirectResponse(static::URL_INDEX);
    }

    /**
     * @param Request $request
     * @param int|null $idCred
     *
     * @return mixed
     */
    protected function handleSaveForm(Request $request, ?int $idCred = null)
    {
        $dataProvider = $this
            ->getFactory()
            ->createCredentialsFormDataProvider();

        $form = $this
            ->getFactory()
            ->createCredentialsForm(
                $dataProvider->getData($idCred),
                $dataProvider->getOptions($idCred)
            )
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this
                    ->getFacade()
                    ->save($form->getData());

                $this->addSuccessMessage('credentials saved successfully');
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
