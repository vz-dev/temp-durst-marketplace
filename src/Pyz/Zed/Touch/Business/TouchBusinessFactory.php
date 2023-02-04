<?php
/**
 * Durst - project - TouchBusinessFactory.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 07.12.18
 * Time: 13:16
 */

namespace Pyz\Zed\Touch\Business;

use Pyz\Zed\Touch\Business\Util\Search\TouchSearchTruncater;
use Pyz\Zed\Touch\Business\Util\Search\TouchSearchTruncaterInterface;
use Pyz\Zed\Touch\Business\Util\TimeStampUpdater;
use Pyz\Zed\Touch\Business\Util\TimeStampUpdaterInterface;
use Spryker\Zed\Touch\Business\TouchBusinessFactory as SprykerTouchBusinessFactory;

/**
 * Class TouchBusinessFactory
 * @package Pyz\Zed\Touch\Business
 * @method \Pyz\Zed\Touch\Persistence\TouchQueryContainerInterface getQueryContainer()
 */
class TouchBusinessFactory extends SprykerTouchBusinessFactory
{
    /**
     * @return \Pyz\Zed\Touch\Business\Util\TimeStampUpdaterInterface
     */
    public function createTimeStampUpdater(): TimeStampUpdaterInterface
    {
        return new TimeStampUpdater(
            $this->getQueryContainer()
        );
    }

    /**
     * @return \Pyz\Zed\Touch\Business\Util\Search\TouchSearchTruncaterInterface
     */
    public function createTouchSearchTruncater(): TouchSearchTruncaterInterface
    {
        return new TouchSearchTruncater(
            $this->getQueryContainer()
        );
    }
}
