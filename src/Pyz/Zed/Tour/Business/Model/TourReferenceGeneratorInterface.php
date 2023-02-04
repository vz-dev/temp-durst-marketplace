<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 25.10.18
 * Time: 12:44
 */

namespace Pyz\Zed\Tour\Business\Model;


use Generated\Shared\Transfer\ConcreteTourTransfer;

interface TourReferenceGeneratorInterface
{
    /**
     * @param ConcreteTourTransfer $concreteTourTransfer
     * @return string
     */
    public function generateTourReference(ConcreteTourTransfer $concreteTourTransfer) : string;

}
