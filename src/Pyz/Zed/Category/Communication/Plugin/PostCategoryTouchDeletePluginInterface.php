<?php
/**
 * Durst - project - PostCategoryTouchDeletePluginInterface.phpe.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 12.11.18
 * Time: 11:15
 */

namespace Pyz\Zed\Category\Communication\Plugin;


interface PostCategoryTouchDeletePluginInterface
{
    /**
     * @param int $idCategory
     * @return void
     */
    public function postCategoryDelete(int $idCategory);
}