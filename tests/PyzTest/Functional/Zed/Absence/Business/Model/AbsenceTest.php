<?php
namespace PyzTest\Functional\Zed\Absence\Business\Model;

use Generated\Shared\Transfer\AbsenceTransfer;
use Generated\Shared\Transfer\BranchTransfer;
use Orm\Zed\Absence\Persistence\SpyAbsence;
use Orm\Zed\Absence\Persistence\SpyAbsenceQuery;
use PHPUnit\Framework\MockObject\MockObject;
use Pyz\Zed\Absence\Business\Exception\AbsenceNotFoundException;
use Pyz\Zed\Absence\Business\Exception\InvalidBranchException;
use Pyz\Zed\Absence\Business\Exception\StartAfterEndException;
use Pyz\Zed\Absence\Business\Model\Absence;
use Pyz\Zed\Absence\Persistence\AbsenceQueryContainer;
use Pyz\Zed\Merchant\Business\MerchantFacade;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;

class AbsenceTest extends \Codeception\Test\Unit
{
    public const START_TIME = '20.08.2018';
    public const END_TIME = '10.09.2018';
    public const DESCRIPTION = 'testDescription';

    /**
     * @var \PyzTest\Functional\Zed\Absence\AbsenceBusinessTester
     */
    protected $tester;

    /**
     * @var MerchantFacadeInterface|MockObject
     */
    protected $merchantFacade;

    /**
     * @var BranchTransfer
     */
    protected $branchTransfer;

    /**
     * @var Absence
     */
    protected $absenceModel;

    /**
     * {@inheritdoc}
     */
    protected function _before()
    {
        $this->merchantFacade = $this->createMerchantFacade();
        $this->branchTransfer = $this->createBranchTransfer();

        $this->absenceModel = new Absence(
            new AbsenceQueryContainer(),
            $this->merchantFacade
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function _after()
    {
    }

    /**
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\Absence\Business\Exception\StartAfterEndException
     */
    public function testAddAbsence()
    {
        $this->merchantFacade->expects($this->once())
            ->method('getCurrentBranch')
            ->will($this->returnValue($this->branchTransfer));

        $absenceTransfer = $this->absenceModel->addAbsence($this->createAbsenceTransfer());

        $this->assertNotNull($absenceTransfer->getIdAbsence());
        $this->assertEquals(static::START_TIME, $absenceTransfer->getStartDate());
        $this->assertEquals(static::END_TIME, $absenceTransfer->getEndDate());
        $this->assertEquals(static::DESCRIPTION, $absenceTransfer->getDescription());
    }

    /**
     * @return void
     * @throws StartAfterEndException
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function testAddAbsenceThrowsExceptionIfStartAfterEnd()
    {
        $this->expectException(StartAfterEndException::class);

        $this->absenceModel->addAbsence(
            $this->createAbsenceTransfer(
                '20.08.2018',
                '19.08.2018'
            )
        );

    }

    /**
     * @skip
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\Absence\Business\Exception\StartAfterEndException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function testGetAbsencesForCurrentBranch()
    {
        $this->merchantFacade->expects($this->atLeastOnce())
            ->method('getCurrentBranch')
            ->will($this->returnValue($this->branchTransfer));


        foreach ($this->createThreeAbsences() as $absence) {
            $this->absenceModel->addAbsence($absence);
        }

        $absences = $this->absenceModel->getAbsencesForCurrentBranch();

        $this->assertCount(3, $absences);
    }

    /**
     * @skip
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\Absence\Business\Exception\StartAfterEndException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function testGetAbsencesForCurrentBranchWontReturnAbsencesFromDifferentBranch()
    {
        $absenceForDifferentBranch = new SpyAbsence();
        $absenceForDifferentBranch->setFkBranch(3);
        $absenceForDifferentBranch->setStartDate(new \DateTime('-7 day'));
        $absenceForDifferentBranch->setEndDate(new \DateTime('+7 day'));
        $absenceForDifferentBranch->setDescription('Absence for different branch');
        $absenceForDifferentBranch->save();

        $this->merchantFacade->expects($this->atLeastOnce())
            ->method('getCurrentBranch')
            ->will($this->returnValue($this->branchTransfer));

        foreach ($this->createThreeAbsences() as $absence) {
            $this->absenceModel->addAbsence($absence);
        }

        $absences = $this->absenceModel->getAbsencesForCurrentBranch();

        $this->assertCount(3, $absences);
    }

    /**
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\Absence\Business\Exception\AbsenceNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     * @throws \Pyz\Zed\Absence\Business\Exception\InvalidBranchException
     */
    public function testRemoveAbsenceById()
    {
        $absenceEntity = new SpyAbsence();
        $absenceEntity->setFkBranch(1);
        $absenceEntity->setStartDate(new \DateTime('-7 day'));
        $absenceEntity->setEndDate(new \DateTime('+7 day'));
        $absenceEntity->setDescription('Absence for current branch');
        $absenceEntity->save();

        $this->assertNotNull($absenceEntity->getIdAbsence());

        $this->merchantFacade->expects($this->atLeastOnce())
            ->method('getCurrentBranch')
            ->will($this->returnValue($this->branchTransfer));

        $this->absenceModel->removeAbsenceById($absenceEntity->getIdAbsence());

        $absenceEntity = SpyAbsenceQuery::create()
            ->findOneByIdAbsence($absenceEntity->getIdAbsence());

        $this->assertNull($absenceEntity);
    }

    /**
     * @return void
     * @throws AbsenceNotFoundException
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\Absence\Business\Exception\InvalidBranchException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function testRemoveAbsenceByIdThrowsExceptionIfAbsenceDoesNotBelongToCurrentBranch()
    {
        $absenceEntity = new SpyAbsence();
        $absenceEntity->setFkBranch(3);
        $absenceEntity->setStartDate(new \DateTime('-7 day'));
        $absenceEntity->setEndDate(new \DateTime('+7 day'));
        $absenceEntity->setDescription('Absence for different branch');
        $absenceEntity->save();

        $this->assertNotNull($absenceEntity->getIdAbsence());

        $this->merchantFacade->expects($this->atLeastOnce())
            ->method('getCurrentBranch')
            ->will($this->returnValue($this->branchTransfer));

        $this->expectException(InvalidBranchException::class);

        $this->absenceModel->removeAbsenceById($absenceEntity->getIdAbsence());
    }

    /**
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function testBranchIsAbsentForAbsentBranch()
    {
        $absenceEntity = new SpyAbsence();
        $absenceEntity->setFkBranch(1);
        $absenceEntity->setStartDate(new \DateTime('-7 day'));
        $absenceEntity->setEndDate(new \DateTime('+7 day'));
        $absenceEntity->setDescription('Absence for current branch');
        $absenceEntity->save();

        $this->assertNotNull($absenceEntity->getIdAbsence());

        $isAbsent = $this->absenceModel->isBranchAbsent(
            1,
            new \DateTime('-1 day'),
            new \DateTime('+1 day')
        );

        $this->assertEquals(true, $isAbsent);
    }

    /**
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function testBranchIsAbsentForNotAbsentBranch()
    {
        $absenceEntity = new SpyAbsence();
        $absenceEntity->setFkBranch(1);
        $absenceEntity->setStartDate(new \DateTime('-7 day'));
        $absenceEntity->setEndDate(new \DateTime('+7 day'));
        $absenceEntity->setDescription('Absence for current branch');
        $absenceEntity->save();

        $this->assertNotNull($absenceEntity->getIdAbsence());

        $isAbsent = $this->absenceModel->isBranchAbsent(
            1,
            new \DateTime('-10 day'),
            new \DateTime('-9 day')
        );

        $this->assertEquals(false, $isAbsent);

        $absenceEntity = new SpyAbsence();
        $absenceEntity->setFkBranch(3);
        $absenceEntity->setStartDate(new \DateTime('-11 day'));
        $absenceEntity->setEndDate(new \DateTime('-7 day'));
        $absenceEntity->setDescription('Absence for different branch');
        $absenceEntity->save();

        $this->assertNotNull($absenceEntity->getIdAbsence());

        $isAbsent = $this->absenceModel->isBranchAbsent(
            1,
            new \DateTime('-10 day'),
            new \DateTime('-9 day')
        );

        $this->assertEquals(false, $isAbsent);
    }

    /**
     * @return MockObject|MerchantFacadeInterface
     */
    protected function createMerchantFacade()
    {
        $merchantFacade = $this->getMockBuilder(MerchantFacade::class)->setMethods(
            ['getCurrentBranch',]
        )->getMock();

        return $merchantFacade;
    }

    /**
     * @return BranchTransfer
     */
    protected function createBranchTransfer() : BranchTransfer
    {
        return (new BranchTransfer())
            ->setFkMerchant(1)
            ->setIdBranch(1)
            ->setName('Durst Köln')
            ->setEmail('max@mustermann.com');
    }

    /**
     * @return BranchTransfer
     */
    protected function createBranchTransferForDifferentBranch() : BranchTransfer
    {
        return (new BranchTransfer())
            ->setFkMerchant(2)
            ->setIdBranch(3)
            ->setName('Getränke Schneider')
            ->setEmail('karl@schneider.com');
    }

    /**
     * @param string $start
     * @param string $end
     * @param string $description
     * @return AbsenceTransfer
     */
    protected function createAbsenceTransfer(
        string $start = self::START_TIME,
        string $end = self::END_TIME,
        string $description = self::DESCRIPTION
    ) : AbsenceTransfer
    {
        return (new AbsenceTransfer())
            ->setStartDate($start)
            ->setEndDate($end)
            ->setDescription($description);
    }

    /**
     * @return array|AbsenceTransfer[]
     */
    protected function createThreeAbsences() : array
    {
        $absences = [];
        $absences[] = (new AbsenceTransfer())
            ->setDescription('firstAbsence')
            ->setStartDate('20.08.2018')
            ->setEndDate('30.08.2018');


        $absences[] = (new AbsenceTransfer())
            ->setDescription('firstAbsence')
            ->setStartDate('20.09.2018')
            ->setEndDate('30.09.2018');

        $absences[] = (new AbsenceTransfer())
            ->setDescription('firstAbsence')
            ->setStartDate('10.08.2018')
            ->setEndDate('11.08.2018');

        return $absences;
    }
}
