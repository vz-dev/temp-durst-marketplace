<?php
/**
 * Durst - project - CategoryHydrator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 17.05.18
 * Time: 09:10
 */

namespace Pyz\Yves\AppRestApi\Handler\Hydrator\Category;


use Generated\Shared\Transfer\AppApiRequestTransfer;
use Generated\Shared\Transfer\CategoryLocalizedAttributesTransfer;
use Generated\Shared\Transfer\CategoryTransfer;
use Pyz\Client\AppRestApi\AppRestApiClientInterface;
use Pyz\Yves\AppRestApi\AppRestApiConfig;
use Pyz\Yves\AppRestApi\Handler\Hydrator\HydratorInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Response\CategoryKeyResponseInterface as Response;

class CategoryHydrator implements HydratorInterface
{
    /**
     * @var AppRestApiClientInterface
     */
    protected $client;

    /**
     * @var AppRestApiConfig
     */
    protected $config;

    /**
     * CategoryHydrator constructor.
     * @param AppRestApiClientInterface $client
     * @param AppRestApiConfig $config
     */
    public function __construct(AppRestApiClientInterface $client, AppRestApiConfig $config)
    {
        $this->client = $client;
        $this->config = $config;
    }

    /**
     * @param \stdClass $requestObject
     * @param \stdClass $responseObject
     * @return mixed|void
     */
    public function hydrate(\stdClass $requestObject, \stdClass $responseObject, string $version = 'v1')
    {
        $responseTransfer = $this
            ->client
            ->getCategoryList(new AppApiRequestTransfer());

        $responseObject->{Response::KEY_CATEGORIES} = [];
        foreach ($responseTransfer->getCategoryList() as $categoryTransfer) {
            $responseObject->{Response::KEY_CATEGORIES}[] = $this->hydrateCategory($categoryTransfer);
        }
    }

    /**
     * @param CategoryTransfer $categoryTransfer
     * @return \stdClass
     */
    protected function hydrateCategory(CategoryTransfer $categoryTransfer) : \stdClass
    {
        $categoryObject = new \stdClass();
        $categoryObject->{Response::KEY_CATEGORY_ID} = $categoryTransfer->getIdCategory();

        if($categoryTransfer->getLocalizedAttributes()->offsetExists(0) !== true){
            return $categoryObject;
        }

        $attributeTransfer = $categoryTransfer->getLocalizedAttributes()->offsetGet(0);

        $this->hydrateAttributes($categoryObject, $attributeTransfer);

        return $categoryObject;

    }

    /**
     * @param \stdClass $categoryObject
     * @param CategoryLocalizedAttributesTransfer $attributeTransfer
     */
    protected function hydrateAttributes(\stdClass $categoryObject, CategoryLocalizedAttributesTransfer $attributeTransfer)
    {
        $categoryObject->{Response::KEY_CATEGORY_NAME} = $attributeTransfer->getName();
        $categoryObject->{Response::KEY_CATEGORY_COLOR_CODE} = $attributeTransfer->getColorCode();
        $categoryObject->{Response::KEY_CATEGORY_IMAGE_URL} = $this->getImageUrl($attributeTransfer);
        $categoryObject->{Response::KEY_CATEGORY_PRIORITY} = $attributeTransfer->getPriority();
    }

    /**
     * @param CategoryLocalizedAttributesTransfer $attributesTransfer
     * @return null|string
     */
    protected function getImageUrl(CategoryLocalizedAttributesTransfer $attributesTransfer)
    {
        if($attributesTransfer->getImageUrl() === null){
            return null;
        }

        $mediaHost = $this
            ->config
            ->getMediaServerHost();

        return sprintf('%s%s', $mediaHost, $attributesTransfer->getImageUrl());
    }
}
