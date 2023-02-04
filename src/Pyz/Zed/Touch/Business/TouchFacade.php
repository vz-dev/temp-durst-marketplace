<?php
/**
 * Durst - project - TouchFacade.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 07.12.18
 * Time: 13:16
 */

namespace Pyz\Zed\Touch\Business;

use Spryker\Zed\Touch\Business\TouchFacade as SprykerTouchFacade;

/**
 * Class TouchFacade
 * @package Pyz\Zed\Touch\Business
 * @method \Pyz\Zed\Touch\Business\TouchBusinessFactory getFactory()
 */
class TouchFacade extends SprykerTouchFacade implements TouchFacadeInterface
{
    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function touchAllNow()
    {
        $this
            ->getFactory()
            ->createTimeStampUpdater()
            ->touchAllNow();
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function removeAllTouchSearchEntries()
    {
        $this
            ->getFactory()
            ->createTouchSearchTruncater()
            ->truncateTouchSearchTable();
    }
}
