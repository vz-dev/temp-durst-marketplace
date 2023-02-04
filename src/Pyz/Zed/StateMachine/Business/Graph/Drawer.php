<?php
/**
 * Durst - project - Drawer.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-07-21
 * Time: 11:51
 */

namespace Pyz\Zed\StateMachine\Business\Graph;


use Spryker\Zed\StateMachine\Business\Process\TransitionInterface;
use Spryker\Zed\StateMachine\Business\Graph\Drawer as SprykerDrawer;

class Drawer extends SprykerDrawer
{
    /**
     * @param \Spryker\Zed\StateMachine\Business\Process\TransitionInterface $transition
     * @param array $label
     *
     * @return array
     */
    protected function addEdgeEventText(TransitionInterface $transition, array $label)
    {
        if ($transition->hasEvent()) {

            /** @var \Pyz\Zed\StateMachine\Business\Process\EventInterface $event */
            $event = $transition->getEvent();

            if ($event->isOnEnter()) {
                $label[] = '<b>' . $event->getName() . ' (on enter)</b>';
            } else {
                $label[] = '<b>' . $event->getName() . '</b>';
            }

            if ($event->hasTimeout()) {
                $label[] = 'timeout: ' . $event->getTimeout();
            }

            if ($event->hasCommand()) {
                $commandLabel = 'command:' . $event->getCommand();

                if (!isset($this->stateMachineHandler->getCommandPlugins()[$event->getCommand()])) {
                    $commandLabel .= ' ' . $this->notImplemented;
                }
                $label[] = $commandLabel;
            }

            if ($event->isManual()) {
                $label[] = 'manually executable';
            }

            if ($event->hasRelativeTimeout()) {
                $columns = explode('.', $event->getRelativeTimeout());
                $tableName = array_shift($columns);
                $label[] = 'relative timeout: ' . implode('&raquo;', $columns);
            }

            if ($event->hasUtcDateTime()) {
                $label[] = 'UTC datetime: ' . explode('.', $event->getUtcDateTime())[1];
            }
        } else {
            $label[] = '&infin;';
        }

        return $label;
    }
}
