<?php
/**
 * Durst - project - PostCategoryTouchActivatePluginInterface.phpe.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 12.11.18
 * Time: 11:18
 */

namespace Pyz\Zed\Category\Communication\Plugin;


interface PostCategoryTouchActivatePluginInterface
{
    /**
     * @param int $idCategory
     * @return void
     */
    public function postCategoryActivate(int $idCategory);
}