<?php
/**
 * Durst - project - SearchConstants.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 27.11.18
 * Time: 18:25
 */

namespace Pyz\Shared\Search;

use Spryker\Shared\Search\SearchConstants as SprykerSearchConstants;

interface SearchConstants extends SprykerSearchConstants
{
    public const ELASTICA_PARAMETER__TIME_SLOT_INDEX_NAME = 'ELASTICA_PARAMETER__TIME_SLOT_INDEX_NAME';
    public const ELASTICA_PARAMETER__TIME_SLOT_DOCUMENT_TYPE = 'ELASTICA_PARAMETER__TIME_SLOT_DOCUMENT_TYPE';
    public const ELASTICA_PARAMETER__GM_TIME_SLOT_INDEX_NAME = 'ELASTICA_PARAMETER__GM_TIME_SLOT_INDEX_NAME';
    public const ELASTICA_PARAMETER__GM_TIME_SLOT_DOCUMENT_TYPE = 'ELASTICA_PARAMETER__GM_TIME_SLOT_DOCUMENT_TYPE';
}
