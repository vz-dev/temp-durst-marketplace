<?php
/**
 * Durst - project - CsvExportBranchPreSaverPlugin.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 16.07.20
 * Time: 13:30
 */

namespace Pyz\Zed\Billing\Communication\Plugin\Merchant;

use Generated\Shared\Transfer\BranchTransfer;
use Orm\Zed\Merchant\Persistence\SpyBranch;
use Pyz\Zed\Merchant\Communication\Plugin\BranchPreSaverPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

class CsvExportBranchPreSaverPlugin extends AbstractPlugin implements BranchPreSaverPluginInterface
{

    /**
     * {@inheritDoc}
     *
     * @param \Orm\Zed\Merchant\Persistence\SpyBranch $entity
     * @param \Generated\Shared\Transfer\BranchTransfer $transfer
     * @return void
     */
    public function saveBranch(SpyBranch $entity, BranchTransfer $transfer): void
    {
        $entity
            ->setExportAccount($transfer->getExportAccount())
            ->setExportContraAccount($transfer->getExportContraAccount())
            ->setExportCsvEnabled($transfer->getExportCsvEnabled());
    }
}
