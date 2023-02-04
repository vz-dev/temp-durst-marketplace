<?php
/**
 * Durst - project - ProductExportNotFoundException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 30.09.20
 * Time: 09:18
 */

namespace Pyz\Zed\ProductExport\Business\Exception;


class ProductExportNotFoundException extends ProductExportException
{
    public const MESSAGE = 'Konnte Export %d nicht finden.';
}
