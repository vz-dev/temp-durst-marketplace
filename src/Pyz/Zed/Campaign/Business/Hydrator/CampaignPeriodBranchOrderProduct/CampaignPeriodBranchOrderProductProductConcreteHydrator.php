<?php
/**
 * Durst - project - CampaignPeriodBranchOrderProductMerchantPriceHydrator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 21.06.21
 * Time: 09:58
 */

namespace Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriodBranchOrderProduct;

use Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer;
use Pyz\Zed\Product\Business\ProductFacadeInterface;
use Spryker\Service\UtilEncoding\UtilEncodingServiceInterface;

class CampaignPeriodBranchOrderProductProductConcreteHydrator implements CampaignPeriodBranchOrderProductHydratorInterface
{
    protected const KEY_NAME = 'name';
    protected const KEY_UNIT = 'unit';

    /**
     * @var \Pyz\Zed\Product\Business\ProductFacadeInterface
     */
    protected $productFacade;

    /**
     * @var \Spryker\Service\UtilEncoding\UtilEncodingServiceInterface
     */
    protected $utilEncodingService;

    /**
     * CampaignPeriodBranchOrderProductProductConcreteHydrator constructor.
     * @param \Pyz\Zed\Product\Business\ProductFacadeInterface $productFacade
     * @param \Spryker\Service\UtilEncoding\UtilEncodingServiceInterface $utilEncodingService
     */
    public function __construct(
        ProductFacadeInterface $productFacade,
        UtilEncodingServiceInterface $utilEncodingService
    )
    {
        $this->productFacade = $productFacade;
        $this->utilEncodingService = $utilEncodingService;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
     * @return void
     * @throws \Spryker\Zed\Product\Business\Exception\MissingProductException
     */
    public function hydrateCampaignPeriodBranchOrderProduct(
        CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
    ): void
    {

        $productConcrete = $this
            ->productFacade
            ->getProductConcrete(
                $campaignPeriodBranchOrderProductTransfer
                    ->getSku()
            );

        $attributes = $productConcrete
            ->getAttributes();

        if (is_string($attributes) === true) {
            $attributes = $this
                ->utilEncodingService
                ->decodeJson(
                    $attributes,
                    true
                );
        }

        $productName = 'n/a';
        $productUnit = 'n/a';

        if (array_key_exists(static::KEY_NAME, $attributes) === true) {
            $productName = $attributes[static::KEY_NAME];
        }
        if (array_key_exists(static::KEY_UNIT, $attributes) === true) {
            $productUnit = $attributes[static::KEY_UNIT];
        }

        $campaignPeriodBranchOrderProductTransfer
            ->setProductConcrete(
                $productConcrete
            )
            ->setProductName(
                $productName
            )
            ->setProductUnit(
                $productUnit
            );
    }
}
