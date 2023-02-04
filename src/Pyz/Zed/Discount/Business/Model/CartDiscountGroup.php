<?php
/**
 * Durst - project - CartDiscountGroup.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 12.04.21
 * Time: 10:42
 */

namespace Pyz\Zed\Discount\Business\Model;


use Generated\Shared\Transfer\CartDiscountGroupDiscountTransfer;
use Generated\Shared\Transfer\CartDiscountGroupTransfer;
use Orm\Zed\Discount\Persistence\DstCartDiscountGroup;
use Orm\Zed\Discount\Persistence\SpyDiscount;
use Pyz\Shared\Discount\DiscountConstants;
use Pyz\Zed\Discount\Business\Exception\CartDiscountGroupNotFound;
use Pyz\Zed\Discount\Persistence\DiscountQueryContainerInterface;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;

class CartDiscountGroup implements CartDiscountGroupInterface
{
    /**
     * @var \Pyz\Zed\Discount\Persistence\DiscountQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var \Pyz\Zed\Merchant\Business\MerchantFacadeInterface
     */
    protected $merchantFacade;

    /**
     * @var array|\Pyz\Zed\Discount\Business\Model\Hydrator\CartDiscountGroup\CartDiscountGroupHydratorInterface[]
     */
    protected $groupHydrators;

    /**
     * @var array|\Pyz\Zed\Discount\Business\Model\Hydrator\CartDiscountGroup\CartDiscountGroupDiscountHydratorInterface[]
     */
    protected $discountHydrators;

    /**
     * @var \Pyz\Zed\Discount\Business\Model\CartDiscountGroupNameGeneratorInterface
     */
    protected $cartDiscountGroupNameGenerator;

    /**
     * CartDiscountGroup constructor.
     * @param \Pyz\Zed\Discount\Business\Model\CartDiscountGroupNameGeneratorInterface $cartDiscountGroupNameGenerator
     * @param \Pyz\Zed\Discount\Persistence\DiscountQueryContainerInterface $queryContainer
     * @param \Pyz\Zed\Merchant\Business\MerchantFacadeInterface $merchantFacade
     * @param array|\Pyz\Zed\Discount\Business\Model\Hydrator\CartDiscountGroup\CartDiscountGroupHydratorInterface[] $groupHydrators
     * @param array|\Pyz\Zed\Discount\Business\Model\Hydrator\CartDiscountGroup\CartDiscountGroupDiscountHydratorInterface[] $discountHydrators
     */
    public function __construct(
        CartDiscountGroupNameGeneratorInterface $cartDiscountGroupNameGenerator,
        DiscountQueryContainerInterface         $queryContainer,
        MerchantFacadeInterface                 $merchantFacade,
        array                                   $groupHydrators,
        array                                   $discountHydrators
    )
    {
        $this->cartDiscountGroupNameGenerator = $cartDiscountGroupNameGenerator;
        $this->queryContainer = $queryContainer;
        $this->merchantFacade = $merchantFacade;
        $this->groupHydrators = $groupHydrators;
        $this->discountHydrators = $discountHydrators;
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idCartDiscountGroup
     * @param int $idBranch
     * @return \Generated\Shared\Transfer\CartDiscountGroupTransfer
     * @throws \Pyz\Zed\Discount\Business\Exception\CartDiscountGroupNotFound
     */
    public function getCartDiscountGroupById(
        int $idCartDiscountGroup,
        int $idBranch
    ): CartDiscountGroupTransfer
    {
        $cartDiscountGroupEntity = $this
            ->queryContainer
            ->queryCartDiscountGroupById(
                $idCartDiscountGroup,
                $idBranch
            )
            ->findOne();

        if (
            $cartDiscountGroupEntity === null ||
            $cartDiscountGroupEntity->getIdCartDiscountGroup() === null
        ) {
            throw new CartDiscountGroupNotFound(
                sprintf(
                    CartDiscountGroupNotFound::MESSAGE,
                    $idCartDiscountGroup
                )
            );
        }

        return $this
            ->entityToTransfer(
                $cartDiscountGroupEntity
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     * @return array
     */
    public function getCartDiscountGroupsByBranch(int $idBranch): array
    {
        $result = [];

        $cartDiscountGroupEntities = $this
            ->queryContainer
            ->queryCartDiscountGroupByBranch(
                $idBranch
            )
            ->find();

        foreach ($cartDiscountGroupEntities as $cartDiscountGroupEntity) {
            $result[] = $this
                ->entityToTransfer(
                    $cartDiscountGroupEntity
                );
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     *
     * @return array|CartDiscountGroupTransfer[]
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function generateCartDiscountGroups(): array
    {
        $discounts = $this
            ->queryContainer
            ->queryDiscount()
            ->filterByIsActive(true)
            ->filterByDiscountType(DiscountConstants::TYPE_CART_RULE)
            ->find();

        $cartDiscountGroups = [];

        foreach ($discounts as $discount) {
            // no branch == no discount
            if ($discount->getFkBranch() === null) {
                continue;
            }

            // already part of campaign == no discount
            if ($discount->getDstCampaignPeriodBranchOrderProducts()->count() > 0) {
                continue;
            }

            // already belongs to group == no discount
            if ($discount->getDstCartDiscountGroups()->count() > 0) {
                continue;
            }

            $cartDiscountGroup = $this
                ->createCartDiscountGroupFromDiscount(
                    $discount
                );

            // already has a cart group id == no discount
            if ($cartDiscountGroup->getIdCartDiscountGroup() !== null) {
                continue;
            }

            if (
                $cartDiscountGroup->isNew() === true
            ) {
                $cartDiscountGroup
                    ->save();

                $cartDiscountGroups[] = $this
                    ->entityToTransfer(
                        $cartDiscountGroup
                    );
            }
        }

        return $cartDiscountGroups;
    }

    /**
     * @param \Orm\Zed\Discount\Persistence\SpyDiscount $discount
     * @return \Orm\Zed\Discount\Persistence\DstCartDiscountGroup
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function createCartDiscountGroupFromDiscount(SpyDiscount $discount): DstCartDiscountGroup
    {
        $idBranch = $discount
            ->getFkBranch();

        $cartDiscountGroup = $this
            ->findOrCreateCartDiscountGroup(
                $discount
                    ->getIdDiscount()
            );

        if (
            $cartDiscountGroup->isNew() === true
        ) {
        $cartDiscountGroupName = $this
            ->cartDiscountGroupNameGenerator
            ->generateGroupName(
                $idBranch
            );

            $cartDiscountGroup
                ->setFkBranch(
                    $idBranch
                )
                ->setFkDiscount(
                    $discount
                        ->getIdDiscount()
                )
                ->setIsExpiredDiscount(
                    false
                )
                ->setCarouselPriority(
                    1
                )
                ->setIsCarousel(
                    false
                )
                ->setGroupName(
                    $cartDiscountGroupName
                )
                ->setIsMainDiscount(
                    true
                )
                ->setIsActive(
                    $discount
                        ->getIsActive()
                )
                ->setValidFrom(
                    $discount
                        ->getValidFrom()
                )
                ->setValidTo(
                    $discount
                        ->getValidTo()
                )
                ->setCalculatorPlugin(
                    $discount
                        ->getCalculatorPlugin()
                )
                ->setDiscountType(
                    $discount
                        ->getDiscountType()
                )
                ->setDecisionRuleQueryString(
                    $discount
                        ->getDecisionRuleQueryString()
                )
                ->setCollectorQueryString(
                    $discount
                        ->getCollectorQueryString()
                )
                ->setIsDeleted(
                    false
                );
        }

        return $cartDiscountGroup;
    }

    /**
     * @param int $fkDiscount
     * @return \Orm\Zed\Discount\Persistence\DstCartDiscountGroup
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function findOrCreateCartDiscountGroup(int $fkDiscount): DstCartDiscountGroup
    {
        return $this
            ->queryContainer
            ->queryCartDiscountGroup()
            ->filterByFkDiscount(
                $fkDiscount
            )
            ->findOneOrCreate();
    }

    /**
     * @param \Orm\Zed\Discount\Persistence\DstCartDiscountGroup $cartDiscountGroup
     * @return \Generated\Shared\Transfer\CartDiscountGroupTransfer
     */
    protected function entityToTransfer(DstCartDiscountGroup $cartDiscountGroup): CartDiscountGroupTransfer
    {
        $transfer = new CartDiscountGroupTransfer();

        $transfer
            ->fromArray(
                $cartDiscountGroup
                    ->toArray(),
                true
            );

        $this
            ->hydrateGroup(
                $cartDiscountGroup,
                $transfer
            );

        $this
            ->findAllDiscounts(
                $cartDiscountGroup,
                $transfer
            );

        return $transfer;
    }

    /**
     * @param \Orm\Zed\Discount\Persistence\DstCartDiscountGroup $cartDiscountGroup
     * @param \Generated\Shared\Transfer\CartDiscountGroupTransfer $cartDiscountGroupTransfer
     * @return void
     */
    protected function hydrateGroup(
        DstCartDiscountGroup $cartDiscountGroup,
        CartDiscountGroupTransfer $cartDiscountGroupTransfer
    ): void
    {
        foreach ($this->groupHydrators as $groupHydrator) {
            $groupHydrator
                ->hydrateCartDiscountGroup(
                    $cartDiscountGroup,
                    $cartDiscountGroupTransfer
                );
        }
    }

    /**
     * @param \Orm\Zed\Discount\Persistence\DstCartDiscountGroup $cartDiscountGroup
     * @param \Generated\Shared\Transfer\CartDiscountGroupTransfer $cartDiscountGroupTransfer
     */
    protected function findAllDiscounts(
        DstCartDiscountGroup  $cartDiscountGroup,
        CartDiscountGroupTransfer $cartDiscountGroupTransfer
    ): void
    {
        $cartDiscountGroupList = $this
            ->queryContainer
            ->queryCartDiscountGroupList(
                $cartDiscountGroup
                    ->getScopeValue()
            );

        foreach ($cartDiscountGroupList as $item) {
            $cartDiscountGroupDiscountTransfer = new CartDiscountGroupDiscountTransfer();

            $this
                ->hydrateDiscount(
                    $item,
                    $cartDiscountGroupDiscountTransfer
                );

            $cartDiscountGroupTransfer
                ->addDiscounts(
                    $cartDiscountGroupDiscountTransfer
                );
        }
    }

    /**
     * @param \Orm\Zed\Discount\Persistence\DstCartDiscountGroup $cartDiscountGroup
     * @param \Generated\Shared\Transfer\CartDiscountGroupDiscountTransfer $cartDiscountGroupDiscountTransfer
     */
    protected function hydrateDiscount(
        DstCartDiscountGroup $cartDiscountGroup,
        CartDiscountGroupDiscountTransfer $cartDiscountGroupDiscountTransfer
    ): void
    {
        foreach ($this->discountHydrators as $discountHydrator) {
            $discountHydrator
                ->hydrateCartDiscountGroupDiscount(
                    $cartDiscountGroup,
                    $cartDiscountGroupDiscountTransfer
                );
        }
    }
}
