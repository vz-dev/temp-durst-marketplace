<?php

namespace Pyz\Zed\GraphMasters\Business\Handler;

use Generated\Shared\Transfer\GraphMastersApiToursRequestTransfer;
use Generated\Shared\Transfer\GraphMastersApiGetToursResponseTransfer;

interface TourHandlerInterface
{
    /**
     * @param string $depotId
     * @param array|null $tourIds
     * @param array|null $shifts
     *
     * @return GraphMastersApiToursRequestTransfer
     */
    public function createApiToursRequestTransfer(
        string $depotId,
        array $tourIds = null,
        array $shifts = null
    ): GraphMastersApiToursRequestTransfer;

    /**
     * @param GraphMastersApiToursRequestTransfer $toursRequestTransfer
     *
     * @return GraphMastersApiGetToursResponseTransfer|array
     */
    public function getTours(GraphMastersApiToursRequestTransfer $toursRequestTransfer): GraphMastersApiGetToursResponseTransfer;

    /**
     * @param GraphMastersApiToursRequestTransfer $toursRequestTransfer
     */
    public function fixTours(GraphMastersApiToursRequestTransfer $toursRequestTransfer): void;
}
