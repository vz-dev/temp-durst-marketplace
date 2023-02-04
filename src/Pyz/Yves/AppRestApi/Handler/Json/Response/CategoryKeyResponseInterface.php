<?php
/**
 * Durst - project - CategoryKeyResponseInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 17.05.18
 * Time: 10:54
 */

namespace Pyz\Yves\AppRestApi\Handler\Json\Response;

interface CategoryKeyResponseInterface
{
    public const KEY_CATEGORIES = 'categories';
    public const KEY_CATEGORY_ID = 'id';
    public const KEY_CATEGORY_IMAGE_URL = 'image_url';
    public const KEY_CATEGORY_COLOR_CODE = 'color_code';
    public const KEY_CATEGORY_NAME = 'name';
    public const KEY_CATEGORY_PRIORITY = 'priority';
}
