<?php
/**
 * Durst - project - BranchFormDataProvider.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 03.04.18
 * Time: 16:52
 */

namespace Pyz\Zed\MerchantManagement\Communication\Form\DataProvider;

use Generated\Shared\Transfer\BranchTransfer;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface;
use Pyz\Zed\MerchantManagement\Communication\Form\AbstractBranchForm;

class BranchFormDataProvider
{
    /**
     * @var \Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface
     */
    protected $merchantQueryContainer;

    /**
     * @var \Pyz\Zed\Merchant\Business\MerchantFacadeInterface
     */
    protected $merchantFacade;

    /**
     * BranchFormDataProvider constructor.
     * @param MerchantQueryContainerInterface $merchantQueryContainer
     * @param MerchantFacadeInterface $merchantFacade
     */
    public function __construct(
        MerchantQueryContainerInterface $merchantQueryContainer,
        MerchantFacadeInterface $merchantFacade
    )
    {
        $this->merchantQueryContainer = $merchantQueryContainer;
        $this->merchantFacade = $merchantFacade;
    }

    /**
     * @param int $idBranch
     * @return BranchTransfer
     */
    public function getData(int $idBranch) : BranchTransfer
    {
        return $this
            ->merchantFacade
            ->getBranchById($idBranch);
    }

    /**
     * @param int|null $idMerchant
     * @return array
     */
    public function getOptions(?int $idMerchant = null) : array
    {
        $options = [
            AbstractBranchForm::OPTION_MERCHANT_OPTIONS => $this->getMerchantOptions(),
            AbstractBranchForm::OPTION_SALUTATION_OPTIONS => $this->getSalutationOptions(),
        ];
        if($idMerchant === null){
            return $options;
        }

        $options[AbstractBranchForm::OPTION_PAYMENT_METHOD_CHOICES] = $this->getPaymentMethodSelectChoices($idMerchant);
        return $options;
    }

    /**
     * @return array
     */
    protected function getMerchantOptions() : array
    {
        $merchantEntities = $this
            ->merchantQueryContainer
            ->queryMerchant()
            ->find();

        $options = [];
        foreach ($merchantEntities as $merchantEntity) {
            $options[$merchantEntity->getCompany()] = $merchantEntity->getIdMerchant();
        }

        return $options;
    }

    /**
     * @return array
     */
    protected function getSalutationOptions() : array
    {
        $salutationEntities = $this
            ->merchantQueryContainer
            ->queryEnumSalutation()
            ->find();

        $options = [];
        foreach ($salutationEntities as $salutationEntity) {
            $options[$salutationEntity->getName()] = $salutationEntity->getIdEnumSalutation();
        }

        return $options;
    }

    /**
     * @param int $idMerchant
     * @return array
     */
    protected function getPaymentMethodSelectChoices(int $idMerchant) : array
    {
        $paymentMethods = $this
            ->merchantFacade
            ->getPossiblePaymentMethodsForBranchByMerchantId($idMerchant);

        $options = [];
        foreach($paymentMethods as $paymentMethod){
            $options[$paymentMethod->getName()] = $paymentMethod->getIdPaymentMethod();
        }

        return $options;
    }
}
