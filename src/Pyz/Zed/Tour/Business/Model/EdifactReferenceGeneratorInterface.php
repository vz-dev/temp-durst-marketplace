<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-01-07
 * Time: 09:10
 */

namespace Pyz\Zed\Tour\Business\Model;


use Generated\Shared\Transfer\BranchTransfer;
use Orm\Zed\Merchant\Persistence\SpyBranch;

interface EdifactReferenceGeneratorInterface
{
    /**
     * Generate data transfer reference
     * Edifact D96a: UNB
     *
     * @param SpyBranch $branch
     * @return string
     */
    public function generateDataTransferReference(SpyBranch $branch): string;

    /**
     * Generate data transfer reference
     * Edifact D96a: UNB
     *
     * @param BranchTransfer $branchTransfer
     * @return string
     */
    public function generateDataTransferReferenceFromTransfer(BranchTransfer $branchTransfer): string;

    /**
     * Generate message reference
     * Edifact D96a: UNH
     *
     * @param SpyBranch $branch
     * @return string
     */
    public function generateMessageReference(SpyBranch $branch): string;

    /**
     * Generate message reference
     * Edifact D96a: UNH
     *
     * @param BranchTransfer $branchTransfer
     * @return string
     */
    public function generateMessageReferenceFromTransfer(BranchTransfer $branchTransfer): string;

    /**
     * Generate data transfer reference for deposit / invoice in
     * Edifact D96a: UNB
     *
     * @param SpyBranch $branch
     * @return string
     */
    public function generateDepositReference(SpyBranch $branch): string;

    /**
     * Generate data transfer reference for deposit / invoice in
     * Edifact D96a: UNB
     *
     * @param BranchTransfer $branchTransfer
     * @return string
     */
    public function generateDepositReferenceFromTransfer(BranchTransfer $branchTransfer): string;
}