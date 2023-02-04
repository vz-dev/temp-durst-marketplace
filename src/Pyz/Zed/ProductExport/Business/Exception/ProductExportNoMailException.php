<?php
/**
 * Durst - project - ProductExportNoMailException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 01.10.20
 * Time: 15:54
 */

namespace Pyz\Zed\ProductExport\Business\Exception;


class ProductExportNoMailException extends ProductExportException
{
    public const MESSAGE = 'Für diesen Export existiert keine Mail Adresse.';
}
