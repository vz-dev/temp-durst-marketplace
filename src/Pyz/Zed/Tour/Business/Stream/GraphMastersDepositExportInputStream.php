<?php

namespace Pyz\Zed\Tour\Business\Stream;

use ArrayObject;
use Propel\Runtime\Exception\PropelException;
use Pyz\Shared\Edifact\EdifactConstants;
use Pyz\Zed\Tour\Business\Exception\TourExportException;
use Pyz\Zed\Tour\Business\Mapper\TourExportMapper;
use Pyz\Zed\Tour\Business\Stream\DepositExportInputStream as TourDepositExportInputStream;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

/**
 * Class GraphMastersDepositExportInputStream
 * @package Pyz\Zed\GraphMasters\Business\Stream
 */
class GraphMastersDepositExportInputStream extends TourDepositExportInputStream
{
    /**
     * @return bool
     * @throws AmbiguousComparisonException
     * @throws ContainerKeyNotFoundException
     * @throws PropelException
     * @throws TourExportException
     */
    public function open(): bool
    {
        $graphmastersTourData = $this
            ->depositExportUtil
            ->getGraphmastersTourDataForExport();

        $this->exportVersion = $this->edifactFacade->getExportVersion();

        $exportArray = [];

        $deposits = $this
            ->depositExportUtil
            ->getConsolidatedDeposits();

        if ($this->exportVersion === EdifactConstants::EDIFACT_EXPORT_VERSION_1) {
            $exportArray[] = array_merge($graphmastersTourData, [
                TourExportMapper::PAYLOAD_MERCHANT_SKU => null,
                TourExportMapper::PAYLOAD_QUANTITY => null,
                TourExportMapper::PAYLOAD_DURST_SKU => null,
                TourExportMapper::PAYLOAD_PRODUCT_DESCRIPTION => null,
                TourExportMapper::PAYLOAD_GTIN => null
            ]);
        } else if ($this->exportVersion === EdifactConstants::EDIFACT_EXPORT_VERSION_2) {
            $exportArray[] = array_merge($graphmastersTourData, [
                TourExportMapper::PAYLOAD_ORDER_REFERENCE => null,
                TourExportMapper::PAYLOAD_ORDER_DURST_CUSTOMER_REFERENCE => null,
                TourExportMapper::PAYLOAD_ORDER_ITEMS => null,
            ]);
        }

        if (is_array($deposits) && count($deposits) > 0) {
            foreach ($deposits as $deposit) {
                $exportRow = array_merge(
                    $graphmastersTourData,
                    $deposit
                );

                $exportArray[] = $exportRow;
            }
        }

        $this->iterator = (new ArrayObject($exportArray))
            ->getIterator();

        return true;
    }
}
