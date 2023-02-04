<?php
/**
 * Durst - project - IndexController.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 26.07.18
 * Time: 14:35
 */

namespace Pyz\Zed\SoftwarePackage\Communication\Controller;


use Pyz\Zed\SoftwarePackage\Communication\SoftwarePackageCommunicationFactory;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;

/**
 * Class IndexController
 * @package Pyz\Zed\SoftwarePackage\Communication\Controller
 * @method SoftwarePackageCommunicationFactory getFactory()
 */
class IndexController extends AbstractController
{
    /**
     * @return array
     */
    public function indexAction()
    {
        $softwarePackageTable = $this
            ->getFactory()
            ->createSoftwarePackageTable();

        return [
            'softwarePackageTable' => $softwarePackageTable->render(),
        ];
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function tableAction()
    {
        $softwarePackageTable = $this
            ->getFactory()
            ->createSoftwarePackageTable();

        return $this->jsonResponse(
            $softwarePackageTable->fetchData()
        );
    }
}