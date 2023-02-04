<?php
/**
 * Durst - project - PriceImportNoBranchException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 07.10.20
 * Time: 16:08
 */

namespace Pyz\Zed\PriceImport\Business\Exception;


class PriceImportNoBranchException extends PriceImportException
{
    public const MESSAGE = 'Es wurde kein Branch für den Import gefunden.';
}
