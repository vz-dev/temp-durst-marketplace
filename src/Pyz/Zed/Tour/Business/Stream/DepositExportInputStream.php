<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-01-17
 * Time: 15:21
 */

namespace Pyz\Zed\Tour\Business\Stream;


use ArrayIterator;
use ArrayObject;
use Propel\Runtime\Exception\PropelException;
use Pyz\Shared\Edifact\EdifactConstants;
use Pyz\Zed\Edifact\Business\EdifactFacadeInterface;
use Pyz\Zed\Tour\Business\Exception\ConcreteTourNotExistsException;
use Pyz\Zed\Tour\Business\Exception\TourExportException;
use Pyz\Zed\Tour\Business\Mapper\TourExportMapper;
use Pyz\Zed\Tour\Business\Util\EdiDepositExportUtil;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;
use SprykerMiddleware\Shared\Process\Stream\ReadStreamInterface;
use SprykerMiddleware\Shared\Process\Stream\StreamInterface;

class DepositExportInputStream implements StreamInterface, ReadStreamInterface
{
    /**
     * @var int
     */
    protected $idConcreteTour;

    /**
     * @var EdiDepositExportUtil
     */
    protected $depositExportUtil;

    /**
     * @var ArrayIterator
     */
    protected $iterator;

    /**
     * @var EdifactFacadeInterface
     */
    protected $edifactFacade;

    /**
     * @var string
     */
    protected $exportVersion;

    /**
     * DepositExportInputStream constructor.
     * @param int $idConcreteTour
     * @param EdiDepositExportUtil $depositExportUtil
     * @param EdifactFacadeInterface $edifactFacade
     */
    public function __construct(
        int $idConcreteTour,
        EdiDepositExportUtil $depositExportUtil,
        EdifactFacadeInterface $edifactFacade
    )
    {
        $this->idConcreteTour = $idConcreteTour;
        $this->depositExportUtil = $depositExportUtil;
        $this->edifactFacade = $edifactFacade;
    }

    /**
     * @return array
     */
    public function read(): array
    {
        return $this
            ->get();
    }

    /**
     * @return mixed
     */
    public function get()
    {
        $currentItem = $this
            ->iterator
            ->current();

        $this
            ->iterator
            ->next();

        return $currentItem;
    }

    /**
     * @return bool
     * @throws PropelException
     * @throws ConcreteTourNotExistsException
     * @throws TourExportException
     * @throws AmbiguousComparisonException
     * @throws ContainerKeyNotFoundException
     */
    public function open(): bool
    {
        $tourData = $this
            ->depositExportUtil
            ->getConcreteTourDataForExport();

        $this->exportVersion = $this->edifactFacade->getExportVersion();

        $exportArray = [];

        $deposits = $this
            ->depositExportUtil
            ->getConsolidatedDeposits();

        if ($this->exportVersion === EdifactConstants::EDIFACT_EXPORT_VERSION_1) {
            $exportArray[] = array_merge($tourData, [
                TourExportMapper::PAYLOAD_MERCHANT_SKU => null,
                TourExportMapper::PAYLOAD_QUANTITY => null,
                TourExportMapper::PAYLOAD_DURST_SKU => null,
                TourExportMapper::PAYLOAD_PRODUCT_DESCRIPTION => null,
                TourExportMapper::PAYLOAD_GTIN => null
            ]);
        } else if ($this->exportVersion === EdifactConstants::EDIFACT_EXPORT_VERSION_2) {
            $exportArray[] = array_merge($tourData, [
                TourExportMapper::PAYLOAD_ORDER_REFERENCE => null,
                TourExportMapper::PAYLOAD_ORDER_DURST_CUSTOMER_REFERENCE => null,
                TourExportMapper::PAYLOAD_ORDER_ITEMS => null,
            ]);
        }

        if (is_array($deposits) && count($deposits) > 0) {
            foreach ($deposits as $deposit) {
                $exportRow = array_merge(
                    $tourData,
                    $deposit
                );

                $exportArray[] = $exportRow;
            }
        }

        $this->iterator = (new ArrayObject($exportArray))
            ->getIterator();

        return true;
    }

    /**
     * @return bool
     */
    public function close(): bool
    {
        unset($this->iterator);

        return true;
    }

    /**
     * @param int $offset
     * @param int $whence
     *
     * @return int
     */
    public function seek(int $offset, int $whence): int
    {
        if ($whence === SEEK_SET && $this->iterator->count() > 0) {
            $this
                ->iterator
                ->seek($offset);

            return 0;
        }

        return 1;
    }

    /**
     * @return bool
     */
    public function eof(): bool
    {
        if ($this->iterator->valid() === true) {
            return false;
        }

        return true;
    }
}
