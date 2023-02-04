<?php
/**
 * Durst - project - InvalidExportTypeException.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 06.11.19
 * Time: 11:54
 */

namespace Pyz\Zed\Edifact\Business\Exception;


use Pyz\Shared\Edifact\EdifactConstants;

class InvalidExportTypeException extends \RuntimeException
{
    protected const MESSSAGE = 'The export type %s is not valid. Choose between %s, %s or %s';

    /**
     * @param string $exportType
     * @return static
     */
    public static function invalidWithType(string $exportType): self
    {
        return new InvalidExportTypeException(
            sprintf(
                self::MESSSAGE,
                $exportType,
                EdifactConstants::EDIFACT_EXPORT_TYPE_NON_EDI,
                EdifactConstants::EDIFACT_EXPORT_TYPE_DEPOSIT,
                EdifactConstants::EDIFACT_EXPORT_TYPE_ORDER
            )
        );
    }
}
