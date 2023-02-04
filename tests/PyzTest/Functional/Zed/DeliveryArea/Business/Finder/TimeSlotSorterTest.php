<?php
namespace PyzTest\Functional\Zed\DeliveryArea\Business\Finder;

use Generated\Shared\Transfer\ConcreteTimeSlotTransfer;
use Pyz\Zed\DeliveryArea\Business\Finder\TimeSlotSorter;

class TimeSlotSorterTest extends \Codeception\Test\Unit
{
    protected const FORMAT_TIME = 'H:i';
    protected const START_TIME_H = 10;
    protected const START_TIME_I = '00';

    protected const END_TIME_H = 11;
    protected const END_TIME_I = '30';

    protected const SMALLER_START_TIME = '10:00';
    protected const SMALLER_END_TIME = '11:00';
    protected const BIGGER_START_TIME = '12:00';
    protected const BIGGER_END_TIME = '14:00';

    /**
     * @var \PyzTest\Functional\Zed\DeliveryArea\DeliveryAreaBusinessTester
     */
    protected $tester;

    /**
     * @var array
     */
    protected $timeSlots;

    protected function _before()
    {
        $this->timeSlots[1] = $this->createUnsortedConcreteTimeSlotArray();
    }

    protected function _after()
    {
    }

    // tests
    public function testSortTimeSlotsByStartSortsProperly()
    {
        TimeSlotSorter::sortTimeSlotsByStart($this->timeSlots);

        $this->assertSame('10:00',$this->timeSlots[1][0]->getStartTime());
        $this->assertSame('11:00',$this->timeSlots[1][1]->getStartTime());
        $this->assertSame('12:00',$this->timeSlots[1][2]->getStartTime());
        $this->assertSame('13:00',$this->timeSlots[1][3]->getStartTime());
        $this->assertSame('14:00',$this->timeSlots[1][4]->getStartTime());

        $this->assertSame('11:30',$this->timeSlots[1][0]->getEndTime());
        $this->assertSame('12:30',$this->timeSlots[1][1]->getEndTime());
        $this->assertSame('13:30',$this->timeSlots[1][2]->getEndTime());
        $this->assertSame('14:30',$this->timeSlots[1][3]->getEndTime());
        $this->assertSame('15:30',$this->timeSlots[1][4]->getEndTime());
    }

    public function testSortTimeSlotsByStartSortsForBranchesIndividually()
    {
        $this->timeSlots[14] = $this->createUnsortedConcreteTimeSlotArray();

        TimeSlotSorter::sortTimeSlotsByStart($this->timeSlots);

        foreach ([1, 14] as $idBranch) {
            $this->assertSame('10:00',$this->timeSlots[$idBranch][0]->getStartTime());
            $this->assertSame('11:00',$this->timeSlots[$idBranch][1]->getStartTime());
            $this->assertSame('12:00',$this->timeSlots[$idBranch][2]->getStartTime());
            $this->assertSame('13:00',$this->timeSlots[$idBranch][3]->getStartTime());
            $this->assertSame('14:00',$this->timeSlots[$idBranch][4]->getStartTime());

            $this->assertSame('11:30',$this->timeSlots[$idBranch][0]->getEndTime());
            $this->assertSame('12:30',$this->timeSlots[$idBranch][1]->getEndTime());
            $this->assertSame('13:30',$this->timeSlots[$idBranch][2]->getEndTime());
            $this->assertSame('14:30',$this->timeSlots[$idBranch][3]->getEndTime());
            $this->assertSame('15:30',$this->timeSlots[$idBranch][4]->getEndTime());
        }
    }

    public function testSortLimitedTimeSlotsSortsProperly()
    {
        $this->timeSlots = $this->timeSlots[1];

        TimeSlotSorter::sortLimitedTimeSlots($this->timeSlots);

        $this->assertSame('10:00',$this->timeSlots[0]->getStartTime());
        $this->assertSame('11:00',$this->timeSlots[1]->getStartTime());
        $this->assertSame('12:00',$this->timeSlots[2]->getStartTime());
        $this->assertSame('13:00',$this->timeSlots[3]->getStartTime());
        $this->assertSame('14:00',$this->timeSlots[4]->getStartTime());

        $this->assertSame('11:30',$this->timeSlots[0]->getEndTime());
        $this->assertSame('12:30',$this->timeSlots[1]->getEndTime());
        $this->assertSame('13:30',$this->timeSlots[2]->getEndTime());
        $this->assertSame('14:30',$this->timeSlots[3]->getEndTime());
        $this->assertSame('15:30',$this->timeSlots[4]->getEndTime());
    }

    public function testMergeBranchTimeSlotArraysWithEmptyArray()
    {
        $emptyArray = [];

        /** @var ConcreteTimeSlotTransfer[] $mergedArray */
        $mergedArray = TimeSlotSorter::mergeBranchTimeSlotArrays($emptyArray);
        $this->assertEquals([], $mergedArray);
    }

    public function testMergeBranchTimeSlotArrays()
    {
        $this->timeSlots[14] = $this->createUnsortedConcreteTimeSlotArray();

        $expectedArray = [];
        foreach ([0, 1, 2, 3, 4, 5] as $index) {
            $expectedArray[] = $this->timeSlots[1][$index];
            $expectedArray[] = $this->timeSlots[14][$index];
        }

        /** @var ConcreteTimeSlotTransfer[] $mergedArray */
        $mergedArray = TimeSlotSorter::mergeBranchTimeSlotArrays($this->timeSlots);

        $this->assertEquals($expectedArray, $mergedArray);
    }

    public function testCompareTimeSlotsByStart()
    {
        $smallerTimeSlot = (new ConcreteTimeSlotTransfer())
            ->setIdBranch(1)
            ->setFkTimeSlot(1)
            ->setStartTime(static::SMALLER_START_TIME)
            ->setEndTime(static::SMALLER_END_TIME)
            ->setTimeFormat(static::FORMAT_TIME);

        $biggerTimeSlot = (new ConcreteTimeSlotTransfer())
            ->setIdBranch(1)
            ->setFkTimeSlot(1)
            ->setStartTime(static::BIGGER_START_TIME)
            ->setEndTime(static::BIGGER_END_TIME)
            ->setTimeFormat(static::FORMAT_TIME);

        $comparison = TimeSlotSorter::compareTimeSlotsByStart($smallerTimeSlot, $biggerTimeSlot);

        $this->assertSame(-1, $comparison);

        $comparison = TimeSlotSorter::compareTimeSlotsByStart($biggerTimeSlot, $smallerTimeSlot);

        $this->assertSame(1, $comparison);
    }

    /**
     * @return array
     */
    protected function createUnsortedConcreteTimeSlotArray(): array
    {
        $unsortedTimeSlots = [];

        $counter = 0;

        while($counter < 6){

            $startHours = static::START_TIME_H + $counter;
            $startTime = sprintf('%d:%s', $startHours, static::START_TIME_I);
            $endHours = static::END_TIME_H + $counter;
            $endTime = sprintf('%d:%s', $endHours, static::END_TIME_I);

            $unsortedTimeSlots[] = (new ConcreteTimeSlotTransfer())
                ->setIdBranch(1)
                ->setFkTimeSlot(1)
                ->setStartTime($startTime)
                ->setEndTime($endTime)
                ->setTimeFormat(static::FORMAT_TIME);

            $counter++;
        }

        shuffle($unsortedTimeSlots);

        return $unsortedTimeSlots;
    }
}
