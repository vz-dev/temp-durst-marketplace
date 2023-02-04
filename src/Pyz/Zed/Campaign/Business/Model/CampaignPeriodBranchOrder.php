<?php
/**
 * Durst - project - CampaignPeriodBranchOrder.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 18.06.21
 * Time: 15:42
 */

namespace Pyz\Zed\Campaign\Business\Model;


use DateTime;
use Exception;
use Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer;
use Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer;
use Generated\Shared\Transfer\DiscountConfiguratorTransfer;
use Orm\Zed\Campaign\Persistence\DstCampaignAdvertisingMaterial;
use Orm\Zed\Campaign\Persistence\DstCampaignPeriodBranchOrder;
use Orm\Zed\Campaign\Persistence\DstCampaignPeriodBranchOrderProduct;
use Orm\Zed\Campaign\Persistence\DstCampaignPeriodBranchOrderQuery;
use Orm\Zed\Campaign\Persistence\Map\DstCampaignPeriodTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Pyz\Zed\Campaign\Business\CampaignFacadeInterface;
use Pyz\Zed\Campaign\Business\Exception\CampaignAdvertisingMaterialNotFoundException;
use Pyz\Zed\Campaign\Business\Exception\CampaignPeriodBranchOrderNotFoundException;
use Pyz\Zed\Campaign\Business\Exception\CampaignPeriodBranchOrderSaveFailedException;
use Pyz\Zed\Campaign\CampaignConfig;
use Pyz\Zed\Campaign\Persistence\CampaignQueryContainerInterface;
use Pyz\Zed\Discount\Business\DiscountFacadeInterface;

class CampaignPeriodBranchOrder implements CampaignPeriodBranchOrderInterface
{
    protected const KEY_NEW = 'new';
    protected const KEY_CHANGED = 'changed';
    protected const KEY_REMOVED = 'removed';

    protected const KEY_PRODUCT = 'product';
    protected const KEY_DISCOUNT = 'discount';

    /**
     * @var \Pyz\Zed\Campaign\Persistence\CampaignQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var \Pyz\Zed\Campaign\Business\CampaignFacadeInterface
     */
    protected $facade;

    /**
     * @var \Pyz\Zed\Discount\Business\DiscountFacadeInterface
     */
    protected $discountFacade;

    /**
     * @var \Pyz\Zed\Campaign\CampaignConfig
     */
    protected $config;

    /**
     * @var array|\Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriodBranchOrder\CampaignPeriodBranchOrderHydratorInterface[]
     */
    protected $hydrators;

    /**
     * CampaignPeriodBranchOrder constructor.
     * @param \Pyz\Zed\Campaign\Persistence\CampaignQueryContainerInterface $queryContainer
     * @param \Pyz\Zed\Campaign\Business\CampaignFacadeInterface $facade
     * @param \Pyz\Zed\Discount\Business\DiscountFacadeInterface $discountFacade
     * @param \Pyz\Zed\Campaign\CampaignConfig $config
     * @param array|\Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriodBranchOrder\CampaignPeriodBranchOrderHydratorInterface[] $hydrators
     */
    public function __construct(
        CampaignQueryContainerInterface $queryContainer,
        CampaignFacadeInterface $facade,
        DiscountFacadeInterface $discountFacade,
        CampaignConfig $config,
        array $hydrators
    )
    {
        $this->queryContainer = $queryContainer;
        $this->facade = $facade;
        $this->discountFacade = $discountFacade;
        $this->config = $config;
        $this->hydrators = $hydrators;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer $campaignPeriodBranchOrderTransfer
     * @return \Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer
     * @throws \Pyz\Zed\Campaign\Business\Exception\CampaignPeriodBranchOrderSaveFailedException
     */
    public function saveCampaignPeriodBranchOrder(
        CampaignPeriodBranchOrderTransfer $campaignPeriodBranchOrderTransfer
    ): CampaignPeriodBranchOrderTransfer
    {
        $this
            ->queryContainer
            ->getConnection()
            ->beginTransaction();

        try {
            $entity = $this
                ->findCampaignPeriodBranchOrderOrCreate(
                    $campaignPeriodBranchOrderTransfer
                );

            $entity = $this
                ->hydrateCampaignPeriodBranchOrder(
                    $entity,
                    $campaignPeriodBranchOrderTransfer
                );

            foreach ($campaignPeriodBranchOrderTransfer->getProducts() as $product) {
                $productEntity = $this
                    ->findCampaignPeriodBranchOrderProductOrCreate(
                        $product
                    );
                $productEntity = $this
                    ->hydrateCampaignPeriodBranchOrderProduct(
                        $campaignPeriodBranchOrderTransfer,
                        $productEntity,
                        $product
                    );

                $idDiscount = null;

                if ($product->getDiscount() !== null) {
                    $idDiscount = $this
                        ->discountFacade
                        ->saveDiscount(
                            $product
                                ->getDiscount()
                        );
                }

                $productEntity
                    ->setFkDiscount(
                        $idDiscount
                    );

                $entity
                    ->addDstCampaignPeriodBranchOrderProduct(
                        $productEntity
                    );
            }

            $entity
                ->save();

            $this
                ->queryContainer
                ->getConnection()
                ->commit();

            return $this
                ->entityToTransfer(
                    $entity
                );

        } catch (Exception $exception) {
            $this
                ->queryContainer
                ->getConnection()
                ->rollBack();

            throw new CampaignPeriodBranchOrderSaveFailedException(
                CampaignPeriodBranchOrderSaveFailedException::MESSAGE
            );
        }
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer $campaignPeriodBranchOrderTransfer
     * @return \Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer
     * @throws \Pyz\Zed\Campaign\Business\Exception\CampaignPeriodBranchOrderSaveFailedException
     */
    public function updateCampaignPeriodBranchOrder(
        CampaignPeriodBranchOrderTransfer $campaignPeriodBranchOrderTransfer
    ): CampaignPeriodBranchOrderTransfer
    {
        $this
            ->queryContainer
            ->getConnection()
            ->beginTransaction();

        try {
            $entity = $this
                ->findCampaignPeriodBranchOrderOrCreate(
                    $campaignPeriodBranchOrderTransfer
                );

            $entity = $this
                ->hydrateCampaignPeriodBranchOrder(
                    $entity,
                    $campaignPeriodBranchOrderTransfer
                );

            $splitProducts = $this
                ->splitProductsIntoTypes(
                    $campaignPeriodBranchOrderTransfer
                );

            foreach ($splitProducts as $type => $products) {
                foreach ($products as $product) {
                    /* @var $discount DiscountConfiguratorTransfer */
                    $discount = $product[static::KEY_DISCOUNT];

                    /* @var $currentProduct DstCampaignPeriodBranchOrderProduct */
                    $currentProduct = $product[static::KEY_PRODUCT];

                    switch ($type) {
                        case static::KEY_REMOVED:
                            if ($discount !== null) {
                                $discount
                                    ->getDiscountGeneral()
                                    ->setIsActive(
                                        false
                                    );
                                $this
                                    ->discountFacade
                                    ->updateDiscount(
                                        $discount
                                    );
                            }

                            $currentProduct
                                ->save();

                            $entity
                                ->removeDstCampaignPeriodBranchOrderProduct(
                                    $currentProduct
                                );

                            break;
                        case static::KEY_CHANGED:
                        case static::KEY_NEW:
                            $discountId = null;

                            if ($discount !== null) {
                                $discountId = $this
                                ->saveDiscount(
                                    $discount
                                );
                            }

                            $currentProduct
                                ->setFkDiscount(
                                    $discountId
                                );

                            $currentProduct
                                ->save();

                            $entity
                                ->addDstCampaignPeriodBranchOrderProduct(
                                    $currentProduct
                                );

                            break;
                    }
                }
            }

            $entity
                ->save();

            $this
                ->queryContainer
                ->getConnection()
                ->commit();

            return $this
                ->entityToTransfer(
                    $entity
                );

        } catch (Exception $exception) {
            $this
                ->queryContainer
                ->getConnection()
                ->rollBack();

            throw new CampaignPeriodBranchOrderSaveFailedException(
                CampaignPeriodBranchOrderSaveFailedException::MESSAGE
            );
        }
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idCampaignPeriodBranchOrder
     * @return \Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer
     * @throws \Pyz\Zed\Campaign\Business\Exception\CampaignPeriodBranchOrderNotFoundException
     */
    public function getCampaignPeriodBranchOrderById(
        int $idCampaignPeriodBranchOrder
    ): CampaignPeriodBranchOrderTransfer
    {
        $branchOrder = $this
            ->queryContainer
            ->queryCampaignPeriodBranchOrder()
            ->findOneByIdCampaignPeriodBranchOrder(
                $idCampaignPeriodBranchOrder
            );

        if ($branchOrder === null) {
            throw new CampaignPeriodBranchOrderNotFoundException(
                sprintf(
                    CampaignPeriodBranchOrderNotFoundException::MESSAGE,
                    $idCampaignPeriodBranchOrder
                )
            );
        }

        return $this
            ->entityToTransfer(
                $branchOrder
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     * @return array|CampaignPeriodBranchOrderTransfer[]
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getCampaignPeriodBranchOrdersForBranch(
        int $idBranch
    ): array
    {
        $branchOrders = $this
            ->queryContainer
            ->queryCampaignPeriodBranchOrder()
            ->useDstCampaignPeriodQuery()
                ->filterByIsActive(true)
                ->orderByCampaignStartDate(
                    Criteria::DESC
                )
            ->endUse()
            ->filterByFkBranch($idBranch)
            ->find();

        $result = [];

        foreach ($branchOrders as $branchOrder) {
            $branchOrderTransfer = $this
                ->entityToTransfer(
                    $branchOrder
                );

            $result[] = $branchOrderTransfer;
        }

        return $result;
    }

    /**
     * @param int $idBranch
     * @return DstCampaignPeriodBranchOrderQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getAvailableCampaignPeriodBranchOrdersForCampaignIdsQuery(
        array $campaignIds,
        int $idBranch
    ): DstCampaignPeriodBranchOrderQuery
    {
        return $this
            ->queryContainer
            ->queryCampaignPeriodBranchOrder()
            ->useDstCampaignPeriodQuery()
            ->filterByIsActive(true)
            ->orderByCampaignStartDate(
                Criteria::DESC
            )
            ->where(
                DstCampaignPeriodTableMap::COL_ID_CAMPAIGN_PERIOD . ' IN ?', $campaignIds
            )
            ->endUse()
            ->filterByFkBranch($idBranch);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     * @return array|CampaignPeriodBranchOrderTransfer[]
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getCampaignPeriodBranchOrdersForBranchQuery(
        int $idBranch
    ): DstCampaignPeriodBranchOrderQuery
    {
        return $this
            ->queryContainer
            ->queryCampaignPeriodBranchOrder()
            ->useDstCampaignPeriodQuery()
            ->filterByIsActive(true)
            ->orderByCampaignStartDate(
                Criteria::DESC
            )
            ->endUse()
            ->filterByFkBranch($idBranch);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idCampaignPeriod
     * @param int $idBranch
     * @return bool
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function isCampaignPeriodOrderedByBranch(
        int $idCampaignPeriod,
        int $idBranch
    ): bool
    {
        $branchOrderCount = $this
            ->queryContainer
            ->queryCampaignPeriodBranchOrder()
            ->filterByFkCampaignPeriod(
                $idCampaignPeriod
            )
            ->filterByFkBranch(
                $idBranch
            )
            ->count();

        return ($branchOrderCount > 0);
    }

    /**
     * @param \Orm\Zed\Campaign\Persistence\DstCampaignPeriodBranchOrder $branchOrder
     * @return \Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer
     */
    public function entityToTransfer(
        DstCampaignPeriodBranchOrder $branchOrder
    ): CampaignPeriodBranchOrderTransfer
    {
        $transfer = new CampaignPeriodBranchOrderTransfer();

        $transfer
            ->fromArray(
                $branchOrder
                    ->toArray(),
                true
            );

        foreach ($this->hydrators as $hydrator) {
            $hydrator
                ->hydrateCampaignPeriodBranchOrder(
                    $transfer
                );
        }

        return $transfer;
    }

    /**
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer $branchOrderTransfer
     * @return \Orm\Zed\Campaign\Persistence\DstCampaignPeriodBranchOrder
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function findCampaignPeriodBranchOrderOrCreate(
        CampaignPeriodBranchOrderTransfer $branchOrderTransfer
    ): DstCampaignPeriodBranchOrder
    {
        if ($branchOrderTransfer->getIdCampaignPeriodBranchOrder() === null) {
            return new DstCampaignPeriodBranchOrder();
        }

        return $this
            ->queryContainer
            ->queryCampaignPeriodBranchOrder()
            ->filterByIdCampaignPeriodBranchOrder(
                $branchOrderTransfer
                    ->getIdCampaignPeriodBranchOrder()
            )
            ->findOneOrCreate();
    }

    /**
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer $branchOrderProductTransfer
     * @return \Orm\Zed\Campaign\Persistence\DstCampaignPeriodBranchOrderProduct
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function findCampaignPeriodBranchOrderProductOrCreate(
        CampaignPeriodBranchOrderProductTransfer $branchOrderProductTransfer
    ): DstCampaignPeriodBranchOrderProduct
    {
        if ($branchOrderProductTransfer->getIdCampaignPeriodBranchOrderProduct() === null) {
            return new DstCampaignPeriodBranchOrderProduct();
        }

        return $this
            ->queryContainer
            ->queryCampaignPeriodBranchOrderProduct()
            ->filterByIdCampaignPeriodBranchOrderProduct(
                $branchOrderProductTransfer
                    ->getIdCampaignPeriodBranchOrderProduct()
            )
            ->findOneOrCreate();
    }

    /**
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer $branchOrderTransfer
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer $branchOrderProductTransfer
     * @return \Generated\Shared\Transfer\DiscountConfiguratorTransfer
     * @throws \Exception
     */
    protected function findDiscountConfiguratorTransferOrCreate(
        CampaignPeriodBranchOrderTransfer $branchOrderTransfer,
        CampaignPeriodBranchOrderProductTransfer $branchOrderProductTransfer
    ): DiscountConfiguratorTransfer
    {
        if ($branchOrderProductTransfer->getFkDiscount() === null) {
            $displayNameGenerator = $this
                ->discountFacade
                ->getDiscountDisplayNameGenerator();

            $displayName = $displayNameGenerator
                ->generateDisplayName(
                    $branchOrderTransfer
                    ->getFkBranch()
                );

            $startDate = $branchOrderTransfer
                ->getCampaignPeriod()
                ->getCampaignStartDate();

            if (is_string($startDate)) {
                $startDate = new DateTime($startDate);
            }

            $endDate = $branchOrderTransfer
                ->getCampaignPeriod()
                ->getCampaignEndDate();

            if (is_string($endDate)) {
                $endDate = new DateTime($endDate);
            }

            return $this
                ->discountFacade
                ->createDiscountConfiguratorTransfer(
                    $branchOrderProductTransfer
                        ->getFkBranch(),
                    $displayName,
                    $this
                        ->config
                        ->getDiscountName(),
                    $branchOrderProductTransfer
                        ->getSku(),
                    round($branchOrderProductTransfer->getDiscountPriceValue()),
                    $startDate,
                    $endDate,
                    true
                );
        }

        return $this
            ->discountFacade
            ->getDiscountConfiguratorTransferById(
                $branchOrderProductTransfer
                    ->getFkDiscount()
            );
    }

    /**
     * @param \Orm\Zed\Campaign\Persistence\DstCampaignPeriodBranchOrder $campaignPeriodBranchOrderEntity
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer $campaignPeriodBranchOrderTransfer
     * @return \Orm\Zed\Campaign\Persistence\DstCampaignPeriodBranchOrder
     */
    protected function hydrateCampaignPeriodBranchOrder(
        DstCampaignPeriodBranchOrder $campaignPeriodBranchOrderEntity,
        CampaignPeriodBranchOrderTransfer $campaignPeriodBranchOrderTransfer
    ): DstCampaignPeriodBranchOrder
    {
        return $campaignPeriodBranchOrderEntity
            ->setIdCampaignPeriodBranchOrder(
                $campaignPeriodBranchOrderTransfer
                    ->getIdCampaignPeriodBranchOrder()
            )
            ->setFkBranch(
                $campaignPeriodBranchOrderTransfer
                    ->getFkBranch()
            )
            ->setFkCampaignPeriod(
                $campaignPeriodBranchOrderTransfer
                    ->getFkCampaignPeriod()
            );
    }

    /**
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer $campaignPeriodBranchOrderTransfer
     * @param \Orm\Zed\Campaign\Persistence\DstCampaignPeriodBranchOrderProduct $campaignPeriodBranchOrderProductEntity
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
     * @return \Orm\Zed\Campaign\Persistence\DstCampaignPeriodBranchOrderProduct
     * @throws \Pyz\Zed\Campaign\Business\Exception\CampaignAdvertisingMaterialNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function hydrateCampaignPeriodBranchOrderProduct(
        CampaignPeriodBranchOrderTransfer $campaignPeriodBranchOrderTransfer,
        DstCampaignPeriodBranchOrderProduct $campaignPeriodBranchOrderProductEntity,
        CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
    ): DstCampaignPeriodBranchOrderProduct
    {
        $entity = $campaignPeriodBranchOrderProductEntity
            ->setIdCampaignPeriodBranchOrderProduct(
                $campaignPeriodBranchOrderProductTransfer
                    ->getIdCampaignPeriodBranchOrderProduct()
            )
            ->setSku(
                $campaignPeriodBranchOrderProductTransfer
                    ->getSku()
            )
            ->setCarouselPriority(
                $campaignPeriodBranchOrderProductTransfer
                    ->getCarouselPriority()
            )
            ->setIsExpiredDiscount(
                $campaignPeriodBranchOrderProductTransfer
                    ->getIsExpiredDiscount()
            )
            ->setIsCarousel(
                $campaignPeriodBranchOrderProductTransfer
                    ->getIsCarousel()
            )
            ->setFkCampaignPeriodBranchOrder(
                $campaignPeriodBranchOrderTransfer
                    ->getIdCampaignPeriodBranchOrder()
            );

        foreach ($campaignPeriodBranchOrderProductTransfer->getCampaignAdvertisingMaterials() as $campaignAdvertisingMaterial) {
            $material = $this
                ->getAdvertisingMaterialEntity(
                    $campaignAdvertisingMaterial
                );

            $entity
                ->addDstCampaignAdvertisingMaterial(
                    $material
                );
        }

        $unusedMaterials = $this
            ->getUnusedAdvertisingMaterials(
                $campaignPeriodBranchOrderProductTransfer
            );

        foreach ($unusedMaterials as $unusedMaterial) {
            $entity
                ->removeDstCampaignAdvertisingMaterial(
                    $unusedMaterial
                );
        }

        return $entity;
    }

    /**
     * @param int $idCampaignAdvertisingMaterial
     * @return \Orm\Zed\Campaign\Persistence\DstCampaignAdvertisingMaterial
     * @throws \Pyz\Zed\Campaign\Business\Exception\CampaignAdvertisingMaterialNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function getAdvertisingMaterialEntity(
        int $idCampaignAdvertisingMaterial
    ): DstCampaignAdvertisingMaterial
    {
        $campaignAdvertisingMaterial = $this
            ->queryContainer
            ->queryCampaignAdvertisingMaterial()
            ->filterByIdCampaignAdvertisingMaterial(
                $idCampaignAdvertisingMaterial
            )
            ->filterByIsActive(
                true
            )
            ->findOne();

        if ($campaignAdvertisingMaterial->getIdCampaignAdvertisingMaterial() === null) {
            throw new CampaignAdvertisingMaterialNotFoundException(
                sprintf(
                    CampaignAdvertisingMaterialNotFoundException::MESSAGE_NO_CAMPAIGN_PERIOD,
                    $idCampaignAdvertisingMaterial
                )
            );
        }

        return $campaignAdvertisingMaterial;
    }

    /**
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer $productTransfer
     * @return array
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function getUnusedAdvertisingMaterials(
        CampaignPeriodBranchOrderProductTransfer $productTransfer
    ): array
    {
        $campaignAdvertisingMaterials = $this
            ->queryContainer
            ->queryCampaignAdvertisingMaterial()
            ->useDstCampaignBranchProductCampaignAdvertisingMaterialQuery()
                ->filterByFkCampaignPeriodBranchOrderProduct(
                    $productTransfer
                        ->getIdCampaignPeriodBranchOrderProduct()
                )
                ->filterByFkCampaignAdvertisingMaterial(
                    $productTransfer
                        ->getCampaignAdvertisingMaterials(),
                    Criteria::NOT_IN
                )
            ->endUse()
            ->filterByIsActive(
                true
            )
            ->find();

        $result = [];

        foreach ($campaignAdvertisingMaterials as $campaignAdvertisingMaterial) {
            $result[] = $campaignAdvertisingMaterial;
        }

        return $result;
    }

    /**
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer $campaignPeriodBranchOrderTransfer
     * @return array|array[]
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\Campaign\Business\Exception\CampaignAdvertisingMaterialNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function splitProductsIntoTypes(
        CampaignPeriodBranchOrderTransfer $campaignPeriodBranchOrderTransfer
    ): array
    {
        $result = [
            static::KEY_CHANGED => [],
            static::KEY_NEW => [],
            static::KEY_REMOVED => []
        ];

        foreach ($campaignPeriodBranchOrderTransfer->getProducts() as $product) {
            $productEntity = $this
                ->findCampaignPeriodBranchOrderProductOrCreate(
                    $product
                );

            $productEntity = $this
                ->hydrateCampaignPeriodBranchOrderProduct(
                    $campaignPeriodBranchOrderTransfer,
                    $productEntity,
                    $product
                );

            $discount = $product
                ->getDiscount();

            if (
                $discount === null &&
                $product->getFkDiscount() !== null
            ) {
                $discount = $this
                    ->discountFacade
                    ->getDiscountConfiguratorTransferById(
                        $product
                            ->getFkDiscount()
                    );

                $discount
                    ->getDiscountGeneral()
                    ->setIsActive(
                        false
                    );
            }

            if ($productEntity->isNew() === true) {
                $result[static::KEY_NEW][] = [
                    static::KEY_PRODUCT => $productEntity,
                    static::KEY_DISCOUNT => $discount
                ];
                continue;
            }

            $result[static::KEY_CHANGED][] = [
                static::KEY_PRODUCT => $productEntity,
                static::KEY_DISCOUNT => $discount
            ];
        }

        $result[static::KEY_REMOVED] = $this
            ->getUnusedProducts(
                $campaignPeriodBranchOrderTransfer
            );

        return $result;
    }

    /**
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer $campaignPeriodBranchOrderTransfer
     * @return array
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function getUnusedProducts(
        CampaignPeriodBranchOrderTransfer $campaignPeriodBranchOrderTransfer
    ): array
    {
        $result = [];

        $products = $this
            ->queryContainer
            ->queryCampaignPeriodBranchOrderProduct()
            ->filterByFkCampaignPeriodBranchOrder(
                $campaignPeriodBranchOrderTransfer
                    ->getIdCampaignPeriodBranchOrder()
            )
            ->filterByIdCampaignPeriodBranchOrderProduct(
                array_filter(
                    $campaignPeriodBranchOrderTransfer
                        ->getOrderedProducts()
                ),
                Criteria::NOT_IN
            )
            ->find();

        foreach ($products as $product) {
            $discount = null;

            if ($product->getFkDiscount() !== null) {
                $discount = $this
                    ->discountFacade
                    ->getDiscountConfiguratorTransferById(
                        $product
                            ->getFkDiscount()
                    );
            }

            foreach ($product->getDstCampaignAdvertisingMaterials() as $dstCampaignAdvertisingMaterial) {
                $product
                    ->removeDstCampaignAdvertisingMaterial(
                        $dstCampaignAdvertisingMaterial
                    );
            }

            $result[] = [
                static::KEY_PRODUCT => $product,
                static::KEY_DISCOUNT => $discount
            ];
        }

        return $result;
    }

    /**
     * @param \Generated\Shared\Transfer\DiscountConfiguratorTransfer $discountConfiguratorTransfer
     * @return int|null
     */
    protected function saveDiscount(
        DiscountConfiguratorTransfer $discountConfiguratorTransfer
    ): int
    {
        if ($discountConfiguratorTransfer->getDiscountGeneral()->getIdDiscount() === null) {
            return $this
                ->discountFacade
                ->saveDiscount(
                    $discountConfiguratorTransfer
                );
        }

        $this
            ->discountFacade
            ->updateDiscount(
                $discountConfiguratorTransfer
            );

        return $discountConfiguratorTransfer
            ->getDiscountGeneral()
            ->getIdDiscount();
    }
}
