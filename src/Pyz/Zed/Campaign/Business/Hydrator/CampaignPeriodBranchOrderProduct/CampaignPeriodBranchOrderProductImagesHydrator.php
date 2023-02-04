<?php
/**
 * Durst - project - CampaignPeriodBranchOrderProductImagesHydrator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 29.07.21
 * Time: 12:48
 */

namespace Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriodBranchOrderProduct;


use Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer;
use Pyz\Zed\Campaign\Business\Utility\ImageUtilInterface;
use Spryker\Service\UtilEncoding\UtilEncodingServiceInterface;

class CampaignPeriodBranchOrderProductImagesHydrator implements CampaignPeriodBranchOrderProductHydratorInterface
{
    public const PRODUCT_IMAGE_TYPE_PRODUCT_LOGO = 'Product logo';
    public const PRODUCT_IMAGE_TYPE_BOTTLE_SHOT = 'Bottleshot';
    public const PRODUCT_IMAGE_TYPE_DETAIL = 'Detail';
    public const PRODUCT_IMAGE_TYPE_CASE_SHOT = 'Caseshot';

    protected const PRODUCT_IMAGE_TYPES = [
        self::PRODUCT_IMAGE_TYPE_PRODUCT_LOGO => 'product_logo',
        self::PRODUCT_IMAGE_TYPE_BOTTLE_SHOT => 'bottleshot_big',
        self::PRODUCT_IMAGE_TYPE_DETAIL => 'picture_detail_1',
        self::PRODUCT_IMAGE_TYPE_CASE_SHOT => 'caseshot_product_unit',
    ];


    /**
     * @var \Pyz\Zed\Campaign\Business\Utility\ImageUtilInterface
     */
    protected $imageUtil;

    /**
     * @var \Spryker\Service\UtilEncoding\UtilEncodingServiceInterface
     */
    protected $utilEncodingService;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * CampaignPeriodBranchOrderProductImagesHydrator constructor.
     * @param \Pyz\Zed\Campaign\Business\Utility\ImageUtilInterface $imageUtil
     * @param \Spryker\Service\UtilEncoding\UtilEncodingServiceInterface $encodingService
     */
    public function __construct(
        ImageUtilInterface $imageUtil,
        UtilEncodingServiceInterface $encodingService
    )
    {
        $this->imageUtil = $imageUtil;
        $this->utilEncodingService = $encodingService;
    }

    /**
     * @inheritDoc
     */
    public function hydrateCampaignPeriodBranchOrderProduct(
        CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
    ): void
    {
        $productConcrete = $campaignPeriodBranchOrderProductTransfer
            ->getProductConcrete();

        $this->attributes = $productConcrete
            ->getAttributes();

        if (is_string($this->attributes) === true) {
            $attributes = $this
                ->utilEncodingService
                ->decodeJson(
                    $this->attributes,
                    true
                );
        }

        $images = [];

        foreach (static::PRODUCT_IMAGE_TYPES as $title => $type) {
            $property = $this
                ->getPropertyFromAttributes(
                    $type
                );

            if ($property === null) {
                continue;
            }

            $images[$title] = $this
                ->imageUtil
                ->formatBig(
                    $property
                );
        }

        $campaignPeriodBranchOrderProductTransfer
            ->setImages(
                $images
            );
    }

    /**
     * @param string $property
     * @return string|null
     */
    protected function getPropertyFromAttributes(string $property): ?string
    {
        if (array_key_exists($property, $this->attributes)) {
            return $this->attributes[$property];
        }

        return null;
    }
}
