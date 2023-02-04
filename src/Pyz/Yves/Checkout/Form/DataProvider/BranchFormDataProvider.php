<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 16.01.18
 * Time: 16:52
 */

namespace Pyz\Yves\Checkout\Form\DataProvider;


use Generated\Shared\Transfer\GetBranchesRequestTransfer;
use Pyz\Client\Merchant\MerchantClientInterface;
use Pyz\Yves\Checkout\Form\Steps\BranchForm;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface;

class BranchFormDataProvider implements StepEngineFormDataProviderInterface
{
    /**
     * @var MerchantClientInterface
     */
    protected $merchantClient;

    /**
     * BranchFormDataProvider constructor.
     * @param MerchantClientInterface $merchantClient
     */
    public function __construct(MerchantClientInterface $merchantClient)
    {
        $this->merchantClient = $merchantClient;
    }


    /**
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer $dataTransfer
     *
     * @return \Spryker\Shared\Kernel\Transfer\AbstractTransfer
     */
    public function getData(AbstractTransfer $dataTransfer)
    {
        return $dataTransfer;
    }

    /**
     * @param AbstractTransfer $dataTransfer
     * @return array
     * @throws \Spryker\Client\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getOptions(AbstractTransfer $dataTransfer)
    {
        return [
            BranchForm::OPTION_BRANCHES => $this->getBranchOptions()
        ];
    }


    /**
     * @return array
     * @throws \Spryker\Client\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getBranchOptions()
    {
        $transfer = $this
            ->merchantClient
            ->getBranches(new GetBranchesRequestTransfer());

        $options = [];
        foreach($transfer->getBranches() as $branch){
            $options[$branch->getIdBranch()] = $branch->getName();
        }

        return $options;
    }
}
