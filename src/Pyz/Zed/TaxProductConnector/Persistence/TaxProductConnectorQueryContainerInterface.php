<?php
/**
 * Durst - project - TaxProductConnectorQueryContainerInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 25.06.20
 * Time: 19:29
 */

namespace Pyz\Zed\TaxProductConnector\Persistence;

use DateTime;
use Spryker\Zed\TaxProductConnector\Persistence\TaxProductConnectorQueryContainerInterface as SprykerTaxProductConnectorQueryContainerInterface;

interface TaxProductConnectorQueryContainerInterface extends SprykerTaxProductConnectorQueryContainerInterface
{
    public function queryTaxSetByIdProductAbstractAndCountryIso2CodeForDate(
        array $allIdProductAbstracts,
        $countryIso2Code,
        DateTime $date
    );
}
