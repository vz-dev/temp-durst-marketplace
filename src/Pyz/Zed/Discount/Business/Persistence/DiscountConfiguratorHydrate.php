<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-03-26
 * Time: 16:17
 */

namespace Pyz\Zed\Discount\Business\Persistence;

use Generated\Shared\Transfer\DiscountConfiguratorTransfer;
use Pyz\Zed\Discount\Persistence\DiscountQueryContainerInterface;
use Spryker\Zed\Discount\Business\Persistence\DiscountConfiguratorHydrate as SprykerDiscountConfiguratorHydrate;

class DiscountConfiguratorHydrate extends SprykerDiscountConfiguratorHydrate
{

    /**
     * @param int $idDiscount
     * @param int $idBranch
     * @return DiscountConfiguratorTransfer
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getByIdDiscountAndIdBranch(int $idDiscount, int $idBranch): DiscountConfiguratorTransfer
    {
        $discountEntity = $this
            ->discountQueryContainer
            ->queryDiscount()
            ->filterByIdDiscount($idDiscount)
            ->filterByFkBranch($idBranch)
            ->findOne();

        $discountConfigurator = $this
            ->createDiscountConfiguratorTransfer();

        $discountGeneralTransfer = $this
            ->hydrateGeneralDiscount($discountEntity);
        $discountConfigurator
            ->setDiscountGeneral($discountGeneralTransfer);

        $discountCalculatorTransfer = $this
            ->hydrateDiscountCalculator($discountEntity);
        $discountConfigurator
            ->setDiscountCalculator($discountCalculatorTransfer);

        $discountConditionTransfer = $this
            ->hydrateDiscountCondition($discountEntity);
        $discountConfigurator
            ->setDiscountCondition($discountConditionTransfer);

        $this
            ->hydrateDiscountVoucher(
                $idDiscount,
                $discountEntity,
                $discountConfigurator
            );

        $discountConfigurator = $this
            ->executeDiscountConfigurationExpanderPlugins($discountConfigurator);

        return $discountConfigurator;
    }
}