<?php
/**
 * Durst - project - ProductExportNoBranchException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 01.10.20
 * Time: 15:57
 */

namespace Pyz\Zed\ProductExport\Business\Exception;


class ProductExportNoBranchException extends ProductExportException
{
    public const MESSAGE = 'Dieser Export hat keine Angaben zum Branch.';
}
