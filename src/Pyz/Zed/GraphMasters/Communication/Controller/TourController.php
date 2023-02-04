<?php

namespace Pyz\Zed\GraphMasters\Communication\Controller;

use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\GraphMasters\Business\Exception\EntityNotFoundException;
use Pyz\Zed\GraphMasters\Business\GraphMastersFacade;
use Pyz\Zed\GraphMasters\Communication\GraphMastersCommunicationFactory;
use Pyz\Zed\GraphMasters\Persistence\GraphMastersQueryContainer;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method GraphMastersFacade getFacade()
 * @method GraphMastersCommunicationFactory getFactory()
 * @method GraphMastersQueryContainer getQueryContainer()
 */
class TourController extends AbstractController
{
    public const PARAM_ID_TOUR = 'id-tour';

    public const URL_INDEX = '/graph-masters/tour';
    public const URL_DETAIL = '/graph-masters/tour/detail';

    /**
     * @return array
     */
    public function indexAction(): array
    {
        $table = $this
            ->getFactory()
            ->createTourTable();

        return [
            'table' => $table->render(),
        ];
    }

    /**
     * @param Request $request
     *
     * @return array
     *
     * @throws EntityNotFoundException
     * @throws PropelException
     */
    public function detailAction(Request $request): array
    {
        $idTour = $this->castId($request->query->getInt(static::PARAM_ID_TOUR));

        $tourTransfer = $this->getFacade()->getTourById($idTour);

        return ['tour' => $tourTransfer];
    }

    /**
     * @return JsonResponse
     */
    public function tableAction(): JsonResponse
    {
        $table = $this
            ->getFactory()
            ->createTourTable();

        return $this->jsonResponse(
            $table->fetchData()
        );
    }
}
