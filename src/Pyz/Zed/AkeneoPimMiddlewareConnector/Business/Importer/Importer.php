<?php
/**
 * Copyright (c) 2018. Durststrecke GmbH. All rights reserved.
 */

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\AkeneoPimMiddlewareConnector\Business\Importer;

use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetStepBrokerInterface;

class Importer implements ImporterInterface
{
    /**
     * @var \Spryker\Zed\DataImport\Business\Model\DataSet\DataSetStepBrokerInterface
     */
    private $dataSetStepBroker;

    /**
     * @var \Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface
     */
    private $dataSet;

    /**
     * Importer constructor.
     * @param DataSetStepBrokerInterface $dataSetStepBroker
     * @param DataSetInterface $dataSet
     */
    public function __construct(
        DataSetStepBrokerInterface $dataSetStepBroker,
        DataSetInterface $dataSet
    ) {
        $this->dataSetStepBroker = $dataSetStepBroker;
        $this->dataSet = $dataSet;
    }

    /**
     * @param array $data
     *
     * @return void
     */
    public function import(array $data): void
    {
        foreach ($data as $item) {
            $this->dataSet->exchangeArray($item);
            $this->dataSetStepBroker->execute($this->dataSet);
        }
    }
}
