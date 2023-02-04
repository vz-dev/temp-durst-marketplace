<?php
/**
 * Durst - project - Category.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 17.06.21
 * Time: 09:12
 */

namespace Pyz\Zed\GraphMasters\Business\Model;


use Generated\Shared\Transfer\GraphMastersDeliveryAreaCategoryTransfer;
use Generated\Shared\Transfer\GraphMastersDeliveryAreaTransfer;
use Orm\Zed\DeliveryArea\Persistence\Map\SpyDeliveryAreaTableMap;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersDeliveryAreaCategory;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersDeliveryAreaCategoryToDeliveryArea;
use Orm\Zed\Merchant\Persistence\Map\SpyBranchTableMap;
use Propel\Runtime\Exception\PropelException;
use Pyz\Shared\GraphMasters\GraphMastersConstants;
use Pyz\Zed\GraphMasters\Persistence\GraphMastersQueryContainerInterface;
use Pyz\Zed\Touch\Business\TouchFacadeInterface;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

class Category implements CategoryInterface
{
    /**
     * @var GraphMastersQueryContainerInterface
     */
    protected $queryContainer;
    /**
     * @var int|null
     */
    protected $currentBranchId;

    /**
     * @var TouchFacadeInterface
     */
    protected $touchFacade;

    /**
     * @param GraphMastersQueryContainerInterface $queryContainer
     * @param TouchFacadeInterface $touchFacade
     * @param int|null $currentBranchId
     */
    public function __construct(
        GraphMastersQueryContainerInterface $queryContainer,
        TouchFacadeInterface $touchFacade,
        int $currentBranchId = null
    ) {
        $this->queryContainer = $queryContainer;
        $this->currentBranchId = $currentBranchId;
        $this->touchFacade = $touchFacade;
    }

    /**
     * @param int $idCategory
     * @param bool $currentBranchOnly
     * @return GraphMastersDeliveryAreaCategoryTransfer
     * @throws AmbiguousComparisonException
     */
    public function getDeliveryAreaCategoryById(
        int $idCategory,
        bool $currentBranchOnly = false
    ): GraphMastersDeliveryAreaCategoryTransfer{
        $query = $this
            ->queryContainer
            ->queryGraphmastersDeliveryAreaCategory()
            ->filterByIdDeliveryAreaCategory($idCategory);

        if ($currentBranchOnly === true) {
            $query->filterByFkBranch($this->currentBranchId);
        }

        $deliveryAreaCategory = $query->findOne();

        /**
         * todo: throw exception if not found
         */

        $deliveryAreaCategoryTransfer =  (new GraphMastersDeliveryAreaCategoryTransfer())
            ->fromArray($deliveryAreaCategory->toArray(), true);

        $deliveryAreas = $this->getDeliveryAreasByCategoryId($idCategory);
        $deliveryAreaIds = [];

        foreach($deliveryAreas as $deliveryArea){
            $deliveryAreaCategoryTransfer->addDeliveryAreas($deliveryArea);
            $deliveryAreaIds[] = $deliveryArea->getFkDeliveryArea();
        }

        $deliveryAreaCategoryTransfer->setDeliveryAreaIds($deliveryAreaIds);

        return $deliveryAreaCategoryTransfer;
    }

    /**
     * @return GraphMastersDeliveryAreaCategoryTransfer[]
     */
    public function getActiveDeliveryAreaCategoriesForActiveBranches() : array
    {
        $deliveryAreaCategories = $this
            ->queryContainer
            ->queryGraphmastersDeliveryAreaCategory()
            ->useSpyBranchQuery()
                ->filterByStatus(SpyBranchTableMap::COL_STATUS_ACTIVE)
            ->endUse()
            ->filterByIsActive(true)
            ->find();

        $deliveryAreaCategoriesTransfer = [];

        foreach ($deliveryAreaCategories as $deliveryAreaCategory) {
            $deliveryAreaCategoriesTransfer[] = $this->categoryEntityToTransfer($deliveryAreaCategory);
        }

        return $deliveryAreaCategoriesTransfer;
    }

    /**
     * @param GraphMastersDeliveryAreaCategoryTransfer $deliveryAreaCategoryTransfer
     * @param int|null $fkBranch
     * @return GraphMastersDeliveryAreaCategoryTransfer
     * @throws AmbiguousComparisonException
     * @throws PropelException
     */
    public function saveDeliveryAreaCategory(
        GraphMastersDeliveryAreaCategoryTransfer $deliveryAreaCategoryTransfer,
        int $fkBranch = null
    ): GraphMastersDeliveryAreaCategoryTransfer {
        $deliveryAreaCategory = $this
            ->getOrCreateDeliveryAreaCategoryEntity($deliveryAreaCategoryTransfer->getIdDeliveryAreaCategory());

        $deliveryAreaCategory->fromArray($deliveryAreaCategoryTransfer->toArray());

        if ($deliveryAreaCategory->getFkBranch() === null && $fkBranch !== null) {
            $deliveryAreaCategory->setFkBranch($fkBranch);
        }

        $newDeliveryAreaIds = $deliveryAreaCategoryTransfer->getDeliveryAreaIds();

        $currentDeliveryAreas = [];
        $currentDeliveryAreaIds = [];

        if($deliveryAreaCategoryTransfer->getIdDeliveryAreaCategory() !== null)
        {
            $currentDeliveryAreas = $this
                ->getDeliveryAreasByCategoryId($deliveryAreaCategoryTransfer->getIdDeliveryAreaCategory());
        }

        foreach($currentDeliveryAreas as $currentDeliveryArea)
        {
            $currentDeliveryAreaIds[] = $currentDeliveryArea->getFkDeliveryArea();
        }

        $deliveryAreaIdReferencesToCreate = array_diff($newDeliveryAreaIds, $currentDeliveryAreaIds);

        foreach ($deliveryAreaIdReferencesToCreate as $deliveryAreaId)
        {
            $deliveryAreaCategory->addDstGraphmastersDeliveryAreaCategoryToDeliveryArea(
                (new DstGraphmastersDeliveryAreaCategoryToDeliveryArea())
                    ->setFkDeliveryAreaCategory($deliveryAreaCategory->getIdDeliveryAreaCategory())
                    ->setFkDeliveryArea($deliveryAreaId)
            );
        }

        $deliveryAreaIdReferencesToDelete = array_diff($currentDeliveryAreaIds, $newDeliveryAreaIds);

        foreach ($deliveryAreaCategory->getDstGraphmastersDeliveryAreaCategoryToDeliveryAreas() as $deliveryAreaReference) {
            if (in_array($deliveryAreaReference->getFkDeliveryArea(), $deliveryAreaIdReferencesToDelete)) {
                $deliveryAreaReference->delete();
            }
        }

        $deliveryAreaCategory->save();

        $this->touchFacade->touchActive(GraphMastersConstants::GRAPHMASTERS_SETTINGS_RESOURCE_TYPE, $deliveryAreaCategory->getFkBranch());

        return $this->categoryEntityToTransfer($deliveryAreaCategory);
    }

    /**
     * @param int $categoryId
     * @return GraphMastersDeliveryAreaTransfer[]
     * @throws AmbiguousComparisonException
     */
    public function getDeliveryAreasByCategoryId(int $categoryId) : array
    {
        $deliveryAreas = $this
            ->queryContainer
            ->queryCategoryToDeliveryArea()
            ->useSpyDeliveryAreaQuery()
            ->endUse()
            ->withColumn(SpyDeliveryAreaTableMap::COL_ZIP_CODE, 'zipcode')
            ->withColumn(SpyDeliveryAreaTableMap::COL_CITY, 'city')
            ->filterByFkDeliveryAreaCategory($categoryId)
            ->find();

        $deliveryAreaTransfers = [];

        foreach ($deliveryAreas as $deliveryArea) {
            $deliveryAreaTransfers[] = $this->deliveryAreaEntityToTransfer($deliveryArea);
        }

        return $deliveryAreaTransfers;
    }

    /**
     * @param int $idBranch
     * @return GraphMastersDeliveryAreaCategoryTransfer[]
     * @throws AmbiguousComparisonException
     */
    public function getDeliveryAreaCategoriesByIdBranch(int $idBranch): array
    {
        $deliveryAreaCategories = $this
            ->queryContainer
            ->queryGraphmastersDeliveryAreaCategory()
            ->useSpyBranchQuery()
                ->filterByIdBranch($idBranch)
            ->endUse()
            ->find();

        $deliveryAreaCategoriesTransfer = [];

        foreach ($deliveryAreaCategories as $deliveryAreaCategory) {
            $deliveryAreaCategoriesTransfer[] = $this->categoryEntityToTransfer($deliveryAreaCategory);
        }

        return $deliveryAreaCategoriesTransfer;
    }

    /**
     * @param int $idDeliveryAreaCategory
     * @throws PropelException
     */
    public function removeDeliveryAreaCategory(int $idDeliveryAreaCategory): void
    {
        $entity = $this
            ->queryContainer
            ->queryGraphmastersDeliveryAreaCategory()
            ->findOneByIdDeliveryAreaCategory($idDeliveryAreaCategory);

        if ($entity !== null) {
            $entity->delete();
        }
    }

    /**
     * @param DstGraphmastersDeliveryAreaCategoryToDeliveryArea $deliveryArea
     * @return GraphMastersDeliveryAreaTransfer
     * @throws PropelException
     */
    protected function deliveryAreaEntityToTransfer(
        DstGraphmastersDeliveryAreaCategoryToDeliveryArea $deliveryArea
    ): GraphMastersDeliveryAreaTransfer {
        return (new GraphMastersDeliveryAreaTransfer())
            ->fromArray($deliveryArea->toArray(), true)
            ->setCityName($deliveryArea->getVirtualColumn('city'))
            ->setZipCode($deliveryArea->getVirtualColumn('zipcode'));
    }

    protected function categoryEntityToTransfer(
        DstGraphmastersDeliveryAreaCategory $deliveryAreaCategory
    ): GraphMastersDeliveryAreaCategoryTransfer {
        $deliveryAreaCategoryTransfer = (new GraphMastersDeliveryAreaCategoryTransfer())
            ->fromArray($deliveryAreaCategory->toArray(), true);

        $deliveryAreas = $this->getDeliveryAreasByCategoryId($deliveryAreaCategory->getIdDeliveryAreaCategory());
        $deliveryAreaIds = [];

        foreach($deliveryAreas as $deliveryArea){
            $deliveryAreaCategoryTransfer->addDeliveryAreas($deliveryArea);
            $deliveryAreaIds[] = $deliveryArea->getFkDeliveryArea();
        }

        $deliveryAreaCategoryTransfer->setDeliveryAreaIds($deliveryAreaIds);

        return $deliveryAreaCategoryTransfer;
    }

    /**
     * @param int|null $idDeliveryAreaCategory
     * @return DstGraphmastersDeliveryAreaCategory
     */
    protected function getOrCreateDeliveryAreaCategoryEntity(?int $idDeliveryAreaCategory) : DstGraphmastersDeliveryAreaCategory
    {
        if($idDeliveryAreaCategory === null){
            return new DstGraphmastersDeliveryAreaCategory();
        }

        return $this
            ->queryContainer
            ->queryGraphmastersDeliveryAreaCategory()
            ->filterByIdDeliveryAreaCategory($idDeliveryAreaCategory)
            ->findOneOrCreate();
    }


    /**
     * @param string $zipCode
     * @param string $branchCode
     * @return bool
     */
    public function getDeliversByZipAndBranchCode(string $zipCode, string $branchCode) : bool
    {
        return ($this
                ->queryContainer
                ->queryGraphmastersDeliveryAreaCategory()
                ->useDstGraphmastersDeliveryAreaCategoryToDeliveryAreaQuery()
                    ->useSpyDeliveryAreaQuery()
                        ->filterByZipCode($zipCode)
                    ->endUse()
                ->endUse()
                ->useSpyBranchQuery()
                    ->filterByCode($branchCode)
                    ->filterByStatus(SpyBranchTableMap::COL_STATUS_ACTIVE)
                ->endUse()
                ->filterByIsActive(true)
                ->find()
                ->count() > 0);
    }

    /**
     * @param string $zipCode
     * @param int $idBranch
     * @return bool
     * @throws AmbiguousComparisonException
     */
    public function getDeliversByZipAndIdBranch(string $zipCode, int $idBranch) : bool
    {
        return ($this
                ->queryContainer
                ->queryGraphmastersDeliveryAreaCategory()
                ->useDstGraphmastersDeliveryAreaCategoryToDeliveryAreaQuery()
                ->useSpyDeliveryAreaQuery()
                ->filterByZipCode($zipCode)
                ->endUse()
                ->endUse()
                ->useSpyBranchQuery()
                ->filterByIdBranch($idBranch)
                ->filterByStatus(SpyBranchTableMap::COL_STATUS_ACTIVE)
                ->endUse()
                ->filterByIsActive(true)
                ->find()
                ->count() > 0);
    }
}
