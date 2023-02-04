<?php
/**
 * Durst - project - CategoryFormDataProvider.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 17.06.21
 * Time: 09:14
 */

namespace Pyz\Zed\GraphMasters\Communication\Form;


use Generated\Shared\Transfer\GraphMastersDeliveryAreaCategoryTransfer;
use Orm\Zed\DeliveryArea\Persistence\Map\SpyDeliveryAreaTableMap;
use Orm\Zed\Merchant\Persistence\Map\SpyBranchTableMap;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\DeliveryArea\Persistence\DeliveryAreaQueryContainerInterface;
use Pyz\Zed\GraphMasters\Business\GraphMastersFacadeInterface;
use Pyz\Zed\GraphMasters\Persistence\GraphMastersQueryContainerInterface;
use Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface;
use RuntimeException;

class CategoryFormDataProvider
{
    protected const SLOT_SIZE_OPTIONS = [1,2,4];

    /**
     * @var GraphMastersQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var GraphMastersFacadeInterface
     */
    protected $facade;

    /**
     * @var MerchantQueryContainerInterface
     */
    protected $merchantQueryContainer;

    /**
     * @var DeliveryAreaQueryContainerInterface
     */
    protected $deliveryAreaQueryContainer;

    /**
     * CategoryFormDataProvider constructor.
     * @param GraphMastersQueryContainerInterface $queryContainer
     * @param MerchantQueryContainerInterface $merchantQueryContainer
     * @param DeliveryAreaQueryContainerInterface $deliveryAreaQueryContainer
     */
    public function __construct(
        GraphMastersQueryContainerInterface $queryContainer,
        GraphMastersFacadeInterface $facade,
        MerchantQueryContainerInterface $merchantQueryContainer,
        DeliveryAreaQueryContainerInterface $deliveryAreaQueryContainer
    ) {
        $this->queryContainer = $queryContainer;
        $this->facade = $facade;
        $this->merchantQueryContainer = $merchantQueryContainer;
        $this->deliveryAreaQueryContainer = $deliveryAreaQueryContainer;
    }

    /**
     * @param int|null $idCategory
     *
     * @throws RuntimeException
     *
     * @return GraphMastersDeliveryAreaCategoryTransfer
     */
    public function getData(?int $idCategory = null): GraphMastersDeliveryAreaCategoryTransfer
    {
        if ($idCategory === null) {
            return new GraphMastersDeliveryAreaCategoryTransfer();
        }

        $transfer = $this
            ->facade
            ->getDeliveryAreaCategoryById($idCategory);


        if ($transfer === null) {
            throw new RuntimeException(sprintf('entity with id %d not found', $idCategory));
        }

        return $transfer;
    }

    /**
     * @param int|null $idIntegraCredentials
     *
     * @return array
     */
    public function getOptions(?int $idCategory = null): array
    {
        return [
            CategoryForm::OPTION_BRANCHES => $this->getBranchIds(),
            CategoryForm::OPTION_SLOT_SIZE => $this->getSlotSizes(),
            CategoryForm::OPTION_DELIVERY_AREA => $this->getDeliveryAreas(),
        ];
    }

    /**
     * @return array
     * @throws PropelException
     */
    protected function getBranchIds(): array
    {
        $branches = $this
            ->merchantQueryContainer
            ->queryBranch()
            ->select([
                SpyBranchTableMap::COL_ID_BRANCH,
                SpyBranchTableMap::COL_NAME,
            ])
            ->find();

        $branchOptions = [];
        foreach ($branches as $branch) {
            $branchOptions[$branch[SpyBranchTableMap::COL_NAME]] = $branch[SpyBranchTableMap::COL_ID_BRANCH];
        }

        return $branchOptions;
    }

    /**
     * @return int[]
     */
    protected function getSlotSizes() : array
    {
        return static::SLOT_SIZE_OPTIONS;
    }

    /**
     * @return array
     * @throws PropelException
     */
    protected function getDeliveryAreas() : array
    {
        $deliveryAreaOptions = $this
            ->deliveryAreaQueryContainer
            ->queryDeliveryArea()
            ->select([
                SpyDeliveryAreaTableMap::COL_ZIP_CODE,
                SpyDeliveryAreaTableMap::COL_ID_DELIVERY_AREA,
                SpyDeliveryAreaTableMap::COL_CITY
            ])
            ->find();

        $deliveryAreas = [];
        foreach ($deliveryAreaOptions as $deliveryArea) {
            $deliveryAreas[sprintf('%s - %s', $deliveryArea[SpyDeliveryAreaTableMap::COL_ZIP_CODE], $deliveryArea[SpyDeliveryAreaTableMap::COL_CITY])] = $deliveryArea[SpyDeliveryAreaTableMap::COL_ID_DELIVERY_AREA];
        }

        return $deliveryAreas;
    }
}
