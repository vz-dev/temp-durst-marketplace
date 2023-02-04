<?php
/**
 * Durst - project - TouchSearchTruncater.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 07.12.18
 * Time: 13:26
 */

namespace Pyz\Zed\Touch\Business\Util\Search;

use Pyz\Zed\Touch\Persistence\TouchQueryContainerInterface;

class TouchSearchTruncater implements TouchSearchTruncaterInterface
{
    /**
     * @var \Pyz\Zed\Touch\Persistence\TouchQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * TouchSearchTruncater constructor.
     *
     * @param \Pyz\Zed\Touch\Persistence\TouchQueryContainerInterface $queryContainer
     */
    public function __construct(TouchQueryContainerInterface $queryContainer)
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @return void
     */
    public function truncateTouchSearchTable()
    {
        $this
            ->queryContainer
            ->queryTouchSearch()
            ->deleteAll();
    }
}
