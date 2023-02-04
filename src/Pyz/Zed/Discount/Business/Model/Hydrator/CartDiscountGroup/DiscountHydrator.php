<?php
/**
 * Durst - project - DiscountHydrator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 12.04.21
 * Time: 10:53
 */

namespace Pyz\Zed\Discount\Business\Model\Hydrator\CartDiscountGroup;


use Generated\Shared\Transfer\CartDiscountGroupDiscountTransfer;
use Orm\Zed\Discount\Persistence\DstCartDiscountGroup;
use Pyz\Zed\Discount\Business\DiscountFacadeInterface;

class DiscountHydrator implements CartDiscountGroupDiscountHydratorInterface
{
    /**
     * @var \Pyz\Zed\Discount\Business\DiscountFacadeInterface
     */
    protected $discountFacade;

    /**
     * DiscountHydrator constructor.
     * @param \Pyz\Zed\Discount\Business\DiscountFacadeInterface $discountFacade
     */
    public function __construct(
        DiscountFacadeInterface $discountFacade
    )
    {
        $this->discountFacade = $discountFacade;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Orm\Zed\Discount\Persistence\DstCartDiscountGroup $cartDiscountGroup
     * @param \Generated\Shared\Transfer\CartDiscountGroupDiscountTransfer $cartDiscountGroupDiscountTransfer
     * @return void
     */
    public function hydrateCartDiscountGroupDiscount(
        DstCartDiscountGroup $cartDiscountGroup,
        CartDiscountGroupDiscountTransfer $cartDiscountGroupDiscountTransfer
    ): void
    {
        $discount = $this
            ->discountFacade
            ->getDiscountConfiguratorTransferById(
                $cartDiscountGroup
                    ->getFkDiscount()
            );

        $cartDiscountGroupDiscountTransfer
            ->setFkCartDiscountGroup(
                $cartDiscountGroup
                    ->getIdCartDiscountGroup()
            )
            ->setIdDiscount(
                $cartDiscountGroup
                    ->getFkDiscount()
            )
            ->setIsExpiredDiscount(
                $cartDiscountGroup
                    ->getIsExpiredDiscount()
            )
            ->setIsCarousel(
                $cartDiscountGroup
                    ->getIsCarousel()
            )
            ->setCarouselPriority(
                $cartDiscountGroup
                    ->getCarouselPriority()
            )
            ->setRank(
                $cartDiscountGroup
                    ->getRank()
            )
            ->setScopeValue(
                $cartDiscountGroup
                    ->getScopeValue()
            )
            ->setDiscount(
                $discount
            );
    }
}
