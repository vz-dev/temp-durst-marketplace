<?php

namespace Pyz\Zed\Tour\Communication\Plugin\Command;

use Generated\Shared\Transfer\BranchTransfer;
use Pyz\Shared\Edifact\EdifactConstants;
use Spryker\Zed\Oms\Communication\Plugin\Oms\Command\AbstractCommand;

abstract class AbstractExportReturn extends AbstractCommand
{
    /**
     * @param BranchTransfer $branch
     * @return string
     */
    protected function getEdiDepositEndpointUrl(BranchTransfer $branch): string
    {
        if ($branch->getEdiExportVersion() !== null
            && $branch->getEdiExportVersion() === EdifactConstants::EDIFACT_EXPORT_VERSION_2
        ) {
            return $branch->getEdiDepositEndpointUrl();
        } else {
            return $branch->getEdiEndpointUrl();
        }
    }
}
