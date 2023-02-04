<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 09.10.18
 * Time: 10:34
 */

namespace Pyz\Zed\Tour\Business\Model\AbstractTourHydrator;


use Generated\Shared\Transfer\AbstractTourTransfer;
use Orm\Zed\Tour\Persistence\DstAbstractTour;

interface AbstractTourHydratorInterface
{
    /**
     * @param DstAbstractTour $abstractTourEntity
     * @param AbstractTourTransfer $abstractTourTransfer
     * @return void
     */
    public function hydrateAbstractTour(
        DstAbstractTour $abstractTourEntity,
        AbstractTourTransfer $abstractTourTransfer
    );
}
