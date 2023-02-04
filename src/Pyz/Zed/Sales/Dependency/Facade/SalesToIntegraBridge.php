<?php
/**
 * Durst - project - SalesToIntegraBridge.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 11.11.20
 * Time: 15:47
 */

namespace Pyz\Zed\Sales\Dependency\Facade;


use Generated\Shared\Transfer\IntegraCredentialsTransfer;
use Pyz\Zed\Integra\Business\IntegraFacadeInterface;

class SalesToIntegraBridge implements SalesToIntegraInterface
{
    /**
     * @var IntegraFacadeInterface
     */
    protected $integraFacade;

    /**
     * SalesToIntegraBridge constructor.
     * @param IntegraFacadeInterface $integraFacade
     */
    public function __construct(
        IntegraFacadeInterface $integraFacade
    )
    {
        $this->integraFacade = $integraFacade;
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     * @return bool
     */
    public function doesBranchUseIntegra(int $idBranch): bool
    {
        return $this
            ->integraFacade
            ->doesBranchUseIntegra(
                $idBranch
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     * @return IntegraCredentialsTransfer
     */
    public function getCredentialsByIdBranch(int $idBranch): IntegraCredentialsTransfer
    {
        return $this
            ->integraFacade
            ->getCredentialsByIdBranch(
                $idBranch
            );
    }
}
