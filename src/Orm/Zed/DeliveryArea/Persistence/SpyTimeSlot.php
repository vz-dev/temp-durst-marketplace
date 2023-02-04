<?php

namespace Orm\Zed\DeliveryArea\Persistence;

use Orm\Zed\DeliveryArea\Persistence\Base\SpyTimeSlot as BaseSpyTimeSlot;
use Orm\Zed\Merchant\Persistence\Base\SpyBranch;

/**
 * Skeleton subclass for representing a row from the 'spy_time_slot' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class SpyTimeSlot extends BaseSpyTimeSlot
{
    /**
     * @return SpyBranch|null
     */
    public function getCachedSpyBranch(): ?SpyBranch
    {
        return $this->aSpyBranch;
    }
}
