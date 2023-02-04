<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-02-14
 * Time: 15:30
 */

namespace Pyz\Zed\Edifact\Communication;


use Pyz\Zed\Edifact\Communication\Table\EdifactExportLogTable;
use Pyz\Zed\Edifact\Persistence\EdifactQueryContainerInterface;
use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;

/**
 * Class EdifactCommunicationFactory
 * @package Pyz\Zed\Edifact\Communication
 * @method EdifactQueryContainerInterface getQueryContainer()
 * @method \Pyz\Zed\Edifact\EdifactConfig getConfig()
 */
class EdifactCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return EdifactExportLogTable
     */
    public function createEdifactExportLogTable(): EdifactExportLogTable
    {
        return new EdifactExportLogTable(
            $this->getQueryContainer(),
            $this->getConfig()
        );
    }
}