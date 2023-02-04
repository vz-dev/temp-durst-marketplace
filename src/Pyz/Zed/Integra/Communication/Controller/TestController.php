<?php
/**
 * Durst - project - TestController.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 19.11.20
 * Time: 12:38
 */

namespace Pyz\Zed\Integra\Communication\Controller;


use Spryker\Zed\Kernel\Communication\Controller\AbstractController;

/**
 * Class TestController
 * @package Pyz\Zed\Integra\Communication\Controller
 * @method \Pyz\Zed\Integra\Business\IntegraFacadeInterface getFacade()
 */
class TestController extends AbstractController
{
    public function indexAction(){
        $this
            ->getFacade()
            ->importOrdersForBranch(8);

        return $this
            ->redirectResponse('/integra');
    }
}
