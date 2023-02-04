<?php

namespace Pyz\Zed\GraphMasters\Business\Model\Tour;

use Generated\Shared\Transfer\GraphMastersSettingsTransfer;
use Propel\Runtime\Exception\PropelException;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

interface TourImporterInterface
{
    /**
     * @throws AmbiguousComparisonException
     * @throws PropelException
     */
    public function importTours(): void;

    /**
     * @param string $tourStart
     * @param GraphMastersSettingsTransfer $settings
     * @return string|null
     */
    public function getCommissioningCutOffTime(string $tourStart, GraphMastersSettingsTransfer $settings): ?string;
}
