<?php
/**
 * Durst - project - CategoryController.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 09.06.21
 * Time: 21:17
 */

namespace Pyz\Zed\GraphMasters\Communication\Controller;


use Exception;
use Pyz\Zed\GraphMasters\Business\GraphMastersFacade;
use Pyz\Zed\GraphMasters\Communication\GraphMastersCommunicationFactory;
use Pyz\Zed\GraphMasters\Persistence\GraphMastersQueryContainer;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CategoryController
 * @package Pyz\Zed\GraphMasters\Communication\Controller
 * @method GraphMastersFacade getFacade()
 * @method GraphMastersCommunicationFactory getFactory()
 * @method GraphMastersQueryContainer getQueryContainer()
 */
class CategoryController extends AbstractController
{
    public const PARAM_ID_CATEGORY = 'id-cat';

    public const URL_EDIT = '/graph-masters/category/edit';
    public const URL_REMOVE = '/graph-masters/category/remove';
    public const URL_INDEX = '/graph-masters/category';

    /**
     * @return array
     */
    public function indexAction()
    {
        $table = $this
            ->getFactory()
            ->createDeliveryAreaCategoryTable();

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
            ->createDeliveryAreaCategoryTable();

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
        $idCat = $this->castId($request->get(static::PARAM_ID_CATEGORY, -1));

        $this
            ->getFacade()
            ->removeDeliveryAreaCategory($idCat);

        $this->addSuccessMessage(
            sprintf(
                'Delivery Area Category #%d erfolgreich gelöscht',
                $idCat
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
        $idCategory = $this->castId($request->get(static::PARAM_ID_CATEGORY, -1));
        if ($idCategory === -1) {
            $idCategory = null;
        }

        return $this->handleSaveForm($request, $idCategory);
    }

    /**
     * @param Request $request
     * @param int|null $idCategory
     *
     * @return mixed
     */
    protected function handleSaveForm(Request $request, ?int $idCategory = null)
    {
        $dataProvider = $this
            ->getFactory()
            ->createCategoryFormDataProvider();

        $form = $this
            ->getFactory()
            ->createCategoryForm(
                $dataProvider->getData($idCategory),
                $dataProvider->getOptions($idCategory)
            )
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this
                    ->getFacade()
                    ->saveDeliveryAreaCategory($form->getData());

                $this->addSuccessMessage('category saved successfully');
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
