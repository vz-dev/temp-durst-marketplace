<?php
/**
 * Durst - project - SetupBusinessFactory.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 06.12.18
 * Time: 12:47
 */

namespace Pyz\Zed\Setup\Business;

use Pyz\Zed\Setup\Business\Model\Cronjobs;
use Spryker\Zed\Setup\Business\SetupBusinessFactory as SprykerSetupBusinessFactory;

/**
 * Class SetupBusinessFactory
 * @package Pyz\Zed\Setup\Business
 * @method \Pyz\Zed\Setup\SetupConfig getConfig()
 */
class SetupBusinessFactory extends SprykerSetupBusinessFactory
{
    /**
     * @return \Pyz\Zed\Setup\Business\Model\Cronjobs|\Spryker\Zed\Setup\Business\Model\Cronjobs
     */
    public function createModelCronjobs()
    {
        return new Cronjobs(
            $this->getConfig()
        );
    }
}
