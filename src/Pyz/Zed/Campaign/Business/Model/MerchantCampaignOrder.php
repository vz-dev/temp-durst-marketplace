<?php
/**
 * Durst - project - MerchantCampaignOrder.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 28.06.21
 * Time: 14:58
 */

namespace Pyz\Zed\Campaign\Business\Model;

use DateTime;
use Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer;
use Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer;
use Generated\Shared\Transfer\CampaignPeriodTransfer;
use Generated\Shared\Transfer\DiscountConfiguratorTransfer;
use Generated\Shared\Transfer\MerchantCampaignOrderProductTransfer;
use Generated\Shared\Transfer\MerchantCampaignOrderTransfer;
use Orm\Zed\Campaign\Persistence\Map\DstCampaignPeriodBranchOrderTableMap;
use Pyz\Zed\Campaign\Business\CampaignFacadeInterface;
use Pyz\Zed\Campaign\Business\Exception\CampaignPeriodBranchOrderNotFoundException;
use Pyz\Zed\Campaign\CampaignConfig;
use Pyz\Zed\Campaign\Persistence\CampaignQueryContainerInterface;
use Pyz\Zed\Discount\Business\DiscountFacadeInterface;

class MerchantCampaignOrder implements MerchantCampaignOrderInterface
{
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
     * MerchantCampaignOrder constructor.
     * @param \Pyz\Zed\Campaign\Persistence\CampaignQueryContainerInterface $queryContainer
     * @param \Pyz\Zed\Campaign\Business\CampaignFacadeInterface $facade
     * @param \Pyz\Zed\Discount\Business\DiscountFacadeInterface $discountFacade
     * @param \Pyz\Zed\Campaign\CampaignConfig $config
     */
    public function __construct(
        CampaignQueryContainerInterface $queryContainer,
        CampaignFacadeInterface $facade,
        DiscountFacadeInterface $discountFacade,
        CampaignConfig $config
    )
    {
        $this->queryContainer = $queryContainer;
        $this->facade = $facade;
        $this->discountFacade = $discountFacade;
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idCampaignPeriod
     * @param int $idBranch
     * @param int|null $idCampaignPeriodBranchOrder
     * @return \Generated\Shared\Transfer\MerchantCampaignOrderTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\Campaign\Business\Exception\CampaignPeriodBranchOrderNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getMerchantCampaignOrderById(
        int $idCampaignPeriod,
        int $idBranch,
        ?int $idCampaignPeriodBranchOrder = null
    ): MerchantCampaignOrderTransfer
    {
        $foundIdCampaignPeriodBranchOrder = $this
            ->findOrderByPeriodAndBranch(
                $idCampaignPeriod,
                $idBranch
            );

        if (
            $idCampaignPeriodBranchOrder === null &&
            $foundIdCampaignPeriodBranchOrder === null
        ) {
            $campaignPeriod = $this
                ->getCampaignPeriodById(
                    $idCampaignPeriod
                );

            return (new MerchantCampaignOrderTransfer())
                ->setFkBranch(
                    $idBranch
                )
                ->setFkCampaignPeriod(
                    $idCampaignPeriod
                )
                ->setCampaignPeriod(
                    $campaignPeriod
                )
                ->setEditable(
                    $campaignPeriod
                        ->getBookable()
                )
                ->addProduct(
                    (new MerchantCampaignOrderProductTransfer())
                        ->setIsDiscounted(
                            true
                        )
                        ->setIsCarousel(
                            true
                        )
                        ->setIsEditable(
                            true
                        )
                );
        }

        if ($idCampaignPeriodBranchOrder === null) {
            $idCampaignPeriodBranchOrder = $foundIdCampaignPeriodBranchOrder;
        }

        return $this
            ->transformCampaignPeriodBranchOrderToMerchantCampaignOrderTransfer(
                $this->getCampaignPeriodBranchOrderByCampaignPeriodAndBranch(
                    $idCampaignPeriod,
                    $idBranch,
                    $idCampaignPeriodBranchOrder
                )
            );
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantCampaignOrderTransfer $merchantCampaignOrderTransfer
     * @return \Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer
     * @throws \Exception
     */
    public function createCampaignPeriodBranchOrderFromMerchantCampaignOrder(
        MerchantCampaignOrderTransfer $merchantCampaignOrderTransfer
    ): CampaignPeriodBranchOrderTransfer
    {
        $campaignPeriodBranchOrder = new CampaignPeriodBranchOrderTransfer();

        $campaignPeriod = $this
            ->getCampaignPeriodById(
                $merchantCampaignOrderTransfer
                    ->getFkCampaignPeriod()
            );

        $campaignPeriodBranchOrder
            ->setFkCampaignPeriod(
                $merchantCampaignOrderTransfer
                    ->getFkCampaignPeriod()
            )
            ->setFkBranch(
                $merchantCampaignOrderTransfer
                    ->getFkBranch()
            )
            ->setIdCampaignPeriodBranchOrder(
                $merchantCampaignOrderTransfer
                    ->getFkCampaignPeriodBranchOrder()
            )
            ->setCampaignPeriod(
                $campaignPeriod
            )
            ->setEditable(
                $campaignPeriod
                    ->getBookable()
            );

        $products = $merchantCampaignOrderTransfer
            ->getProducts();

        foreach ($products as $product) {
            $productTransfer = $this
                ->createCampaignPeriodBranchOrderProductFromMerchantCampaignOrderProduct(
                    $product,
                    $merchantCampaignOrderTransfer
                );

            $campaignPeriodBranchOrder
                ->addProduct(
                    $productTransfer
                )
                ->addOrderedProduct(
                    $productTransfer
                        ->getIdCampaignPeriodBranchOrderProduct()
                );
        }

        return $campaignPeriodBranchOrder;
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantCampaignOrderProductTransfer $campaignOrderProductTransfer
     * @param \Generated\Shared\Transfer\MerchantCampaignOrderTransfer $merchantCampaignOrderTransfer
     * @return \Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer
     * @throws \Exception
     */
    protected function createCampaignPeriodBranchOrderProductFromMerchantCampaignOrderProduct(
        MerchantCampaignOrderProductTransfer $campaignOrderProductTransfer,
        MerchantCampaignOrderTransfer $merchantCampaignOrderTransfer
    ): CampaignPeriodBranchOrderProductTransfer
    {
        $campaignProduct = new CampaignPeriodBranchOrderProductTransfer();

        $discount = $this
            ->getDiscountConfiguratorTransfer(
                $campaignOrderProductTransfer,
                $merchantCampaignOrderTransfer
            );

        $campaignProduct
            ->setFkBranch(
                $merchantCampaignOrderTransfer
                    ->getFkBranch()
            )
            ->setFkCampaignPeriod(
                $merchantCampaignOrderTransfer
                    ->getFkCampaignPeriod()
            )
            ->setSku(
                $campaignOrderProductTransfer
                    ->getSku()
            )
            ->setFkDiscount(
                $campaignOrderProductTransfer
                    ->getFkDiscount()
            )
            ->setDiscount(
                $discount
            )
            ->setIsCarousel(
                $campaignOrderProductTransfer
                    ->getIsCarousel()
            )
            ->setCarouselPriority(
                $campaignOrderProductTransfer
                    ->getCarouselPriority()
            )
            ->setIsExpiredDiscount(
                $campaignOrderProductTransfer
                    ->getIsExpiredDiscount()
            )
            ->setIdCampaignPeriodBranchOrderProduct(
                $campaignOrderProductTransfer
                    ->getFkCampaignPeriodBranchOrderProduct()
            );

        foreach ($campaignOrderProductTransfer->getAssignedMaterials() as $assignedMaterial) {
            $campaignProduct
                ->addAssignedCampaignAdvertisingMaterial(
                    $assignedMaterial
                )
                ->addCampaignAdvertisingMaterials(
                    $assignedMaterial
                        ->getIdCampaignAdvertisingMaterial()
                );
        }

        return $campaignProduct;
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantCampaignOrderProductTransfer $campaignOrderProductTransfer
     * @param \Generated\Shared\Transfer\MerchantCampaignOrderTransfer $merchantCampaignOrderTransfer
     * @return \Generated\Shared\Transfer\DiscountConfiguratorTransfer|null
     * @throws \Exception
     */
    protected function getDiscountConfiguratorTransfer(
        MerchantCampaignOrderProductTransfer $campaignOrderProductTransfer,
        MerchantCampaignOrderTransfer $merchantCampaignOrderTransfer
    ): ?DiscountConfiguratorTransfer
    {
        if ($campaignOrderProductTransfer->getIsDiscounted() !== true) {
            return null;
        }

        if ($campaignOrderProductTransfer->getFkDiscount() !== null) {
            $discount = $this
                ->discountFacade
                ->getDiscountConfiguratorTransferById(
                    $campaignOrderProductTransfer
                        ->getFkDiscount()
                );

            return $this
                ->updateDiscount(
                    $discount,
                    $campaignOrderProductTransfer,
                    $merchantCampaignOrderTransfer
                );
        }

        $discountNameGenerator = $this
            ->discountFacade
            ->getDiscountDisplayNameGenerator();

        $discountName = $discountNameGenerator
            ->generateDisplayName(
                $merchantCampaignOrderTransfer
                    ->getFkBranch()
            );

        $start = $merchantCampaignOrderTransfer
            ->getCampaignPeriod()
            ->getCampaignStartDate();

        if (is_string($start)) {
            $start = new DateTime($start);
        }

        $end = $merchantCampaignOrderTransfer
            ->getCampaignPeriod()
            ->getCampaignEndDate();

        if (is_string($end)) {
            $end = new DateTime($end);
        }

        return $this
            ->discountFacade
            ->createDiscountConfiguratorTransfer(
                $merchantCampaignOrderTransfer
                    ->getFkBranch(),
                $discountName,
                $this
                    ->config
                    ->getDiscountName(),
                $campaignOrderProductTransfer
                    ->getSku(),
                round($campaignOrderProductTransfer->getDiscountPrice()),
                $start,
                $end,
                true
            );
    }

    /**
     * @param int $idCampaignPeriod
     * @param int $idBranch
     * @return int|null
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function findOrderByPeriodAndBranch(
        int $idCampaignPeriod,
        int $idBranch
    ): ?int
    {
        return $this
            ->queryContainer
            ->queryCampaignPeriodBranchOrder()
            ->select(
                [
                    DstCampaignPeriodBranchOrderTableMap::COL_ID_CAMPAIGN_PERIOD_BRANCH_ORDER
                ]
            )
            ->filterByFkCampaignPeriod(
                $idCampaignPeriod
            )
            ->filterByFkBranch(
                $idBranch
            )
            ->findOne();
    }

    /**
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer $branchOrderTransfer
     * @return \Generated\Shared\Transfer\MerchantCampaignOrderTransfer
     */
    protected function transformCampaignPeriodBranchOrderToMerchantCampaignOrderTransfer(
        CampaignPeriodBranchOrderTransfer $branchOrderTransfer
    ): MerchantCampaignOrderTransfer
    {
        $merchantOrder = new MerchantCampaignOrderTransfer();

        $campaignPeriod = $this
            ->getCampaignPeriodById(
                $branchOrderTransfer
                    ->getFkCampaignPeriod()
            );

        $merchantOrder
            ->setFkBranch(
                $branchOrderTransfer
                    ->getFkBranch()
            )
            ->setFkCampaignPeriod(
                $branchOrderTransfer
                    ->getFkCampaignPeriod()
            )
            ->setFkCampaignPeriodBranchOrder(
                $branchOrderTransfer
                    ->getIdCampaignPeriodBranchOrder()
            )
            ->setCampaignPeriod(
                $campaignPeriod
            )
            ->setEditable(
                $campaignPeriod
                    ->getBookable()
            );

        $orderedProducts = $branchOrderTransfer
            ->getOrderedProducts();

        if (is_array($orderedProducts) && count($orderedProducts) > 0) {
            foreach ($orderedProducts as $orderedProduct) {
                $product = $this
                    ->getCampaignPeriodBranchOrderProductById(
                        $orderedProduct
                    );
                $merchantProduct = $this
                    ->transformCampaignPeriodBranchOrderProductToMerchantCampaignOrderProduct(
                        $product
                    );

                $merchantOrder
                    ->addAssignedProduct(
                        $orderedProduct
                    )
                    ->addProduct(
                        $merchantProduct
                    );
            }
        }

        return $merchantOrder;
    }

    /**
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProduct
     * @return \Generated\Shared\Transfer\MerchantCampaignOrderProductTransfer
     */
    protected function transformCampaignPeriodBranchOrderProductToMerchantCampaignOrderProduct(
        CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProduct
    ): MerchantCampaignOrderProductTransfer
    {
        $merchantProduct = new MerchantCampaignOrderProductTransfer();

        $isDiscounted = (
            $campaignPeriodBranchOrderProduct->getFkDiscount() !== null &&
            $campaignPeriodBranchOrderProduct->getDiscount() !== null &&
            $campaignPeriodBranchOrderProduct->getDiscount()->getDiscountGeneral() !== null &&
            $campaignPeriodBranchOrderProduct->getDiscount()->getDiscountGeneral()->getIsActive()
        );

        $discountPrice = null;

        if ($isDiscounted === true) {
            /* @var $money \Generated\Shared\Transfer\MoneyValueTransfer */
            $money = $campaignPeriodBranchOrderProduct
                ->getDiscount()
                ->getDiscountCalculator()
                ->getMoneyValueCollection()
                ->getArrayCopy()[0];

            $discountPrice = $money
                ->getGrossAmount();
        }

        $merchantProduct
            ->setFkCampaignPeriodBranchOrderProduct(
                $campaignPeriodBranchOrderProduct
                    ->getIdCampaignPeriodBranchOrderProduct()
            )
            ->setSku(
                $campaignPeriodBranchOrderProduct
                    ->getSku()
            )
            ->setProductName(
                $campaignPeriodBranchOrderProduct
                    ->getProductName()
            )
            ->setProductUnit(
                $campaignPeriodBranchOrderProduct
                    ->getProductUnit()
            )
            ->setThumbProductImage(
                $campaignPeriodBranchOrderProduct
                    ->getThumbProductImage()
            )
            ->setProductPrice(
                $campaignPeriodBranchOrderProduct
                    ->getProductPriceValue()
            )
            ->setDiscountPrice(
                $discountPrice
            )
            ->setIsDiscounted(
                $isDiscounted
            )
            ->setIsCarousel(
                $campaignPeriodBranchOrderProduct
                    ->getIsCarousel()
            )
            ->setCarouselPriority(
                $campaignPeriodBranchOrderProduct
                    ->getCarouselPriority()
            )
            ->setIsExpiredDiscount(
                $campaignPeriodBranchOrderProduct
                    ->getIsExpiredDiscount()
            )
            ->setFkDiscount(
                $campaignPeriodBranchOrderProduct
                    ->getFkDiscount()
            );

        $isEditable = true;

        foreach ($campaignPeriodBranchOrderProduct->getAssignedCampaignAdvertisingMaterials() as $assignedCampaignAdvertisingMaterial) {
            if ($assignedCampaignAdvertisingMaterial->getDaysLeft() < 1) {
                $isEditable = false;

                $merchantProduct
                    ->addBookedMaterial(
                        $assignedCampaignAdvertisingMaterial
                    )
                    ->addFixed(
                        $assignedCampaignAdvertisingMaterial
                            ->getIdCampaignAdvertisingMaterial()
                    )
                    ->addFormBookedMaterial(
                        $assignedCampaignAdvertisingMaterial
                    );
            } else {
                $merchantProduct
                    ->addAssignedMaterial(
                        $assignedCampaignAdvertisingMaterial
                    )
                    ->addMaterial(
                        $assignedCampaignAdvertisingMaterial
                            ->getIdCampaignAdvertisingMaterial()
                    )
                    ->addFormAssignedMaterial(
                        $assignedCampaignAdvertisingMaterial
                    );
            }
        }

        $merchantProduct
            ->setIsEditable(
                $isEditable
            );

        return $merchantProduct;
    }

    /**
     * @param int $idCampaignPeriod
     * @param int $idBranch
     * @param int $idCampaignPeriodBranchOrder
     * @return \Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer
     * @throws \Pyz\Zed\Campaign\Business\Exception\CampaignPeriodBranchOrderNotFoundException
     */
    protected function getCampaignPeriodBranchOrderByCampaignPeriodAndBranch(
        int $idCampaignPeriod,
        int $idBranch,
        int $idCampaignPeriodBranchOrder
    ): CampaignPeriodBranchOrderTransfer
    {
        $campaignOrder = $this
            ->facade
            ->getCampaignPeriodBranchOrderById(
                $idCampaignPeriodBranchOrder
            );

        if (
            $campaignOrder->getFkBranch() === $idBranch &&
            $campaignOrder->getFkCampaignPeriod() === $idCampaignPeriod
        ) {
            return $campaignOrder;
        }

        throw new CampaignPeriodBranchOrderNotFoundException(
            sprintf(
                CampaignPeriodBranchOrderNotFoundException::MESSAGE,
                $idCampaignPeriodBranchOrder
            )
        );
    }

    /**
     * @param int $idCampaignPeriod
     * @return \Generated\Shared\Transfer\CampaignPeriodTransfer
     */
    protected function getCampaignPeriodById(
        int $idCampaignPeriod
    ): CampaignPeriodTransfer
    {
        return $this
            ->facade
            ->getCampaignPeriodById(
                $idCampaignPeriod
            );
    }

    /**
     * @param int $idCampaignPeriodBranchOrderProduct
     * @return \Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer
     */
    protected function getCampaignPeriodBranchOrderProductById(
        int $idCampaignPeriodBranchOrderProduct
    ): CampaignPeriodBranchOrderProductTransfer
    {
        return $this
            ->facade
            ->getCampaignOrderProductById(
                $idCampaignPeriodBranchOrderProduct
            );
    }

    /**
     * @param \Generated\Shared\Transfer\DiscountConfiguratorTransfer $discount
     * @param \Generated\Shared\Transfer\MerchantCampaignOrderProductTransfer $campaignOrderProductTransfer
     * @param \Generated\Shared\Transfer\MerchantCampaignOrderTransfer $merchantCampaignOrderTransfer
     * @return \Generated\Shared\Transfer\DiscountConfiguratorTransfer
     */
    protected function updateDiscount(
        DiscountConfiguratorTransfer $discount,
        MerchantCampaignOrderProductTransfer $campaignOrderProductTransfer,
        MerchantCampaignOrderTransfer $merchantCampaignOrderTransfer
    ): DiscountConfiguratorTransfer
    {
        $queryString = $this
            ->discountFacade
            ->getQueryStringForBranchAndSku(
                $merchantCampaignOrderTransfer
                    ->getFkBranch(),
                $campaignOrderProductTransfer
                    ->getSku()
            );

        $idDiscountAmount = $discount
            ->getDiscountCalculator()
            ->getMoneyValueCollection()
            ->offsetGet(0)
            ->getIdEntity();

        $money = $this
            ->discountFacade
            ->updateMoneyValueCollectionWithAmount(
                $idDiscountAmount,
                round($campaignOrderProductTransfer->getDiscountPrice())
            );

        $general = $discount
            ->getDiscountGeneral();
        $general
            ->setDiscountSku(
                $campaignOrderProductTransfer
                    ->getSku()
            )
            ->setFkBranch(
                $merchantCampaignOrderTransfer
                    ->getFkBranch()
            )
            ->setIsActive(
                $campaignOrderProductTransfer
                    ->getIsDiscounted()
            );

        $calculator = $discount
            ->getDiscountCalculator();
        $calculator
            ->setCollectorQueryString(
                $queryString
            )
            ->setMoneyValueCollection(
                $money
            );

        $condition = $discount
            ->getDiscountCondition();
        $condition
            ->setDecisionRuleQueryString(
                $queryString
            );

        $discount
            ->setDiscountGeneral(
                $general
            )
            ->setDiscountCalculator(
                $calculator
            )
            ->setDiscountCondition(
                $condition
            );

        return $discount;
    }
}
