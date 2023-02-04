<?php
/**
 * Durst - project - CollectorConstants.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 27.11.18
 * Time: 17:10
 */

namespace Pyz\Shared\Collector;

use Spryker\Shared\Collector\CollectorConstants as SprykerCollectorConstants;

interface CollectorConstants extends SprykerCollectorConstants
{
    public const ELASTICA_PARAMETER__TIME_SLOT_INDEX_NAME = 'ELASTICA_PARAMETER__TIME_SLOT_INDEX_NAME';
    public const ELASTICA_PARAMETER__TIME_SLOT_DOCUMENT_TYPE = 'ELASTICA_PARAMETER__TIME_SLOT_DOCUMENT_TYPE';

    public const ELASTICA_PARAMETER__GM_TIME_SLOT_INDEX_NAME = 'ELASTICA_PARAMETER__GM_TIME_SLOT_INDEX_NAME';
    public const ELASTICA_PARAMETER__GM_TIME_SLOT_DOCUMENT_TYPE = 'ELASTICA_PARAMETER__GM_TIME_SLOT_DOCUMENT_TYPE';
}
