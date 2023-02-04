<?php

namespace Pyz\Zed\Tour\Business\Stream;

use Pyz\Shared\Edifact\EdifactConstants;

class GraphMastersDepositExportOutputStream extends GraphMastersTourExportOutputStream
{
    protected const EXPORT_TYPE = EdifactConstants::EDIFACT_EXPORT_TYPE_DEPOSIT;
}
