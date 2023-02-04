<?php
/**
 * Durst - project - TimeStampUpdater.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 07.12.18
 * Time: 13:11
 */

namespace Pyz\Zed\Touch\Business\Util;

use DateTime;
use Pyz\Zed\Touch\Persistence\TouchQueryContainerInterface;

class TimeStampUpdater implements TimeStampUpdaterInterface
{
    /**
     * @var \Pyz\Zed\Touch\Persistence\TouchQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * TimeStampUpdater constructor.
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
    public function touchAllNow()
    {
        $now = new DateTime('now');
        $this
            ->queryContainer
            ->queryTouch()
            ->update([
                'Touched' => $now,
            ]);

        return;
    }
}
