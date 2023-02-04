<?php
/**
 * Durst - project - CategoryInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 17.06.21
 * Time: 09:12
 */

namespace Pyz\Zed\GraphMasters\Business\Model;


use Generated\Shared\Transfer\GraphMastersDeliveryAreaCategoryTransfer;
use Propel\Runtime\Exception\PropelException;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

interface CategoryInterface
{
    /**
     * @param int $idCategory
     * @param bool $currentBranchOnly
     * @return GraphMastersDeliveryAreaCategoryTransfer
     */
    public function getDeliveryAreaCategoryById(int $idCategory, bool $currentBranchOnly = false): GraphMastersDeliveryAreaCategoryTransfer;

    /**
     * @param GraphMastersDeliveryAreaCategoryTransfer $deliveryAreaCategoryTransfer
     * @param int|null $fkBranch
     * @return GraphMastersDeliveryAreaCategoryTransfer
     * @throws AmbiguousComparisonException
     * @throws PropelException
     */
    public function saveDeliveryAreaCategory(GraphMastersDeliveryAreaCategoryTransfer $deliveryAreaCategoryTransfer, int $fkBranch = null): GraphMastersDeliveryAreaCategoryTransfer;

    /**
     * @param int $categoryId
     * @return array
     */
    public function getDeliveryAreasByCategoryId(int $categoryId) : array;

    /**
     * @param int $idBranch
     * @return GraphMastersDeliveryAreaCategoryTransfer[]
     * @throws AmbiguousComparisonException
     */
    public function getDeliveryAreaCategoriesByIdBranch(int $idBranch): array;

    /**
     * @param int $idDeliveryAreaCategory
     * @throws PropelException
     */
    public function removeDeliveryAreaCategory(int $idDeliveryAreaCategory): void;

    /**
     * @param string $zipCode
     * @param string $branchCode
     * @return bool
     */
    public function getDeliversByZipAndBranchCode(string $zipCode, string $branchCode) : bool;

    /**
     * @param string $zipCode
     * @param int $idBranch
     * @return bool
     */
    public function getDeliversByZipAndIdBranch(string $zipCode, int $idBranch) : bool;
}
