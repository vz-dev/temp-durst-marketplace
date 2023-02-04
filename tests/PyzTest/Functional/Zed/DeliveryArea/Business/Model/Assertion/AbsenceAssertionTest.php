<?php
namespace PyzTest\Functional\Zed\DeliveryArea\Business\Model\Assertion;

use Codeception\Test\Unit;
use DateTime;
use Orm\Zed\Absence\Persistence\SpyAbsence;
use Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlot;
use Orm\Zed\DeliveryArea\Persistence\SpyTimeSlot;
use Pyz\Zed\Absence\Business\AbsenceFacade;
use Pyz\Zed\DeliveryArea\Business\Model\Assertion\AbsenceAssertion;

class AbsenceAssertionTest extends Unit
{
    protected const FK_BRANCH = 8;
    protected const START_TIME = '08:00:00';
    protected const END_TIME = '13:00:00';

    /**
     * @var \PyzTest\Functional\Zed\DeliveryArea\DeliveryAreaBusinessTester
     */
    protected $tester;

    /**
     * @var \Pyz\Zed\DeliveryArea\Business\Model\Assertion\AbsenceAssertion
     */
    protected $absenceAssertion;

    /**
     * @var \Orm\Zed\DeliveryArea\Persistence\SpyTimeSlot
     */
    protected $timeSlot;

    /**
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function _before(): void
    {
        $this->absenceAssertion = new AbsenceAssertion(
            new AbsenceFacade()
        );

        $this
            ->createAbsence();

        $this
            ->createTimeSlot();
    }

    /**
     * @return void
     */
    protected function _after(): void
    {
    }

    /**
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function testConcreteTimeSlotIsBeforeAbsence(): void
    {
        $concreteTimeSlotEntity = (new SpyConcreteTimeSlot())
            ->setSpyTimeSlot($this->timeSlot)
            ->setStartTime(sprintf(
                '%s %s',
                'today',
                static::START_TIME
            ))
            ->setEndTime(sprintf(
                '%s %s',
                'today',
                static::END_TIME
            ));

        $this
            ->assertTrue(
                $this
                ->absenceAssertion
                ->isValid($concreteTimeSlotEntity)
            );
    }

    /**
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function testConcreteTimeSlotIsAfterAbsence(): void
    {
        $concreteTimeSlotEntity = (new SpyConcreteTimeSlot())
            ->setSpyTimeSlot($this->timeSlot)
            ->setStartTime(sprintf(
                '%s %s',
                '+4day',
                static::START_TIME
            ))
            ->setEndTime(sprintf(
                '%s %s',
                '+4day',
                static::END_TIME
            ));

        $this
            ->assertTrue(
                $this
                    ->absenceAssertion
                    ->isValid($concreteTimeSlotEntity)
            );
    }

    /**
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function testConcreteTimeSlotIsInsideAbsence(): void
    {
        $concreteTimeSlotEntity = (new SpyConcreteTimeSlot())
            ->setSpyTimeSlot($this->timeSlot)
            ->setStartTime(sprintf(
                '%s %s',
                '+2day',
                static::START_TIME
            ))
            ->setEndTime(sprintf(
                '%s %s',
                '+2day',
                static::END_TIME
            ));

        $this
            ->assertFalse(
                $this
                    ->absenceAssertion
                    ->isValid($concreteTimeSlotEntity)
            );
    }

    /**
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function testConcreteTimeSlotStartsInsideAbsence(): void
    {
        $concreteTimeSlotEntity = (new SpyConcreteTimeSlot())
            ->setSpyTimeSlot($this->timeSlot)
            ->setStartTime(sprintf(
                '%s %s',
                '+3day',
                static::START_TIME
            ))
            ->setEndTime(sprintf(
                '%s %s',
                '+4day',
                static::END_TIME
            ));

        $this
            ->assertFalse(
                $this
                    ->absenceAssertion
                    ->isValid($concreteTimeSlotEntity)
            );
    }

    /**
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function testConcreteTimeSlotEndsInsideAbsence(): void
    {
        $concreteTimeSlotEntity = (new SpyConcreteTimeSlot())
            ->setSpyTimeSlot($this->timeSlot)
            ->setStartTime(sprintf(
                '%s %s',
                'today',
                static::START_TIME
            ))
            ->setEndTime(sprintf(
                '%s %s',
                '+1day',
                static::END_TIME
            ));

        $this
            ->assertFalse(
                $this
                    ->absenceAssertion
                    ->isValid($concreteTimeSlotEntity)
            );
    }

    /**
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function createAbsence(): void
    {
        $startAbsence = new DateTime('+1day');
        $endAbsence = new DateTime('+3day');

        (new SpyAbsence())
            ->setFkBranch(static::FK_BRANCH)
            ->setStartDate($startAbsence)
            ->setEndDate($endAbsence)
            ->save();
    }

    /**
     * @return void
     */
    protected function createTimeSlot(): void
    {
        $this->timeSlot = (new SpyTimeSlot())
            ->setFkBranch(static::FK_BRANCH);
    }
}
