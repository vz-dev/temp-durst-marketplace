<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 13.09.18
 * Time: 14:16
 */

namespace Pyz\Zed\Tour\Business\Model\Saver;


use Generated\Shared\Transfer\AbstractTourTransfer;

interface AbstractTimeSlotSaverInterface
{
    /**
     * @param AbstractTourTransfer $abstractTourTransfer
     * @return void
     */
    public function saveAbstractTimeSlotsForAbstractTour(AbstractTourTransfer $abstractTourTransfer);

}
