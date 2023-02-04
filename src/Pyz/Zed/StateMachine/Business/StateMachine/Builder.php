<?php
/**
 * Durst - project - Builder.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-07-21
 * Time: 10:43
 */

namespace Pyz\Zed\StateMachine\Business\StateMachine;


use SimpleXMLElement;
use Spryker\Zed\StateMachine\Business\StateMachine\Builder as SprykerBuilder;

class Builder extends SprykerBuilder
{
    protected const EVENT_RELATIVE_TIMEOUT_ATTRIBUTE = 'relativeTimeout';
    protected const EVENT_UTC_DATE_TIME_ATTRIBUTE = 'utcDateTime';

    /**
     * {@inheritDoc}
     *
     * @param \SimpleXMLElement $xmlEvent
     *
     * @return \Spryker\Zed\StateMachine\Business\Process\EventInterface|null
     */
    protected function createEvent(SimpleXMLElement $xmlEvent)
    {
        $eventName = $this->getAttributeString($xmlEvent, self::STATE_NAME_ATTRIBUTE);
        if ($eventName === null) {
            return null;
        }

        /** @var \Pyz\Zed\StateMachine\Business\Process\EventInterface $event */
        $event = clone $this->event;
        $event->setCommand($this->getAttributeString($xmlEvent, self::EVENT_COMMAND_ATTRIBUTE));
        $event->setManual($this->getAttributeBoolean($xmlEvent, self::EVENT_MANUAL_ATTRIBUTE));
        $event->setOnEnter($this->getAttributeBoolean($xmlEvent, self::EVENT_ON_ENTER_ATTRIBUTE));
        $event->setTimeout($this->getAttributeString($xmlEvent, self::EVENT_TIMEOUT_ATTRIBUTE));
        $event->setRelativeTimeout($this->getAttributeString($xmlEvent, self::EVENT_RELATIVE_TIMEOUT_ATTRIBUTE));
        $event->setUtcDateTime($this->getAttributeString($xmlEvent, self::EVENT_UTC_DATE_TIME_ATTRIBUTE));
        $event->setName($eventName);

        return $event;
    }
}
