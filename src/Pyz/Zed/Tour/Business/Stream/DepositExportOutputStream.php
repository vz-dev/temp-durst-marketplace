<?php
/**
 * Durst - project - DepositExportOutputStream.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 06.11.19
 * Time: 11:50
 */

namespace Pyz\Zed\Tour\Business\Stream;


use Pyz\Shared\Edifact\EdifactConstants;

class DepositExportOutputStream extends TourExportOutputStream
{
    protected const EXPORT_TYPE = EdifactConstants::EDIFACT_EXPORT_TYPE_DEPOSIT;
}
