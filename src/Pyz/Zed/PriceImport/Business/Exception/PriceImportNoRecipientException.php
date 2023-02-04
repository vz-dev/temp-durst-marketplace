<?php
/**
 * Durst - project - PriceImportNoRecipientException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 07.10.20
 * Time: 16:06
 */

namespace Pyz\Zed\PriceImport\Business\Exception;


class PriceImportNoRecipientException extends PriceImportException
{
    public const MESSAGE = 'Es wurde keine Mail Adresse für die Benachrichtigung hinterlegt.';
}
