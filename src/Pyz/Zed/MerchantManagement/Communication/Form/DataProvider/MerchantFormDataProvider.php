<?php

namespace Pyz\Zed\MerchantManagement\Communication\Form\DataProvider;

use Generated\Shared\Transfer\MerchantTransfer;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\MerchantManagement\Communication\Form\MerchantForm;
use Orm\Zed\Merchant\Persistence\Map\SpyMerchantTableMap;
use Pyz\Zed\SoftwarePackage\Persistence\SoftwarePackageQueryContainerInterface;
use Spryker\Zed\Acl\Business\AclFacadeInterface;

class MerchantFormDataProvider
{
    /**
     * @var \Pyz\Zed\Merchant\Business\MerchantFacadeInterface
     */
    protected $merchantFacade;

    /**
     * @var SoftwarePackageQueryContainerInterface
     */
    protected $softwarePackageQueryContainer;

    /**
     * @var \Spryker\Zed\Acl\Business\AclFacadeInterface
     */
    protected $aclFacade;

    /**
     * MerchantFormDataProvider constructor.
     * @param MerchantFacadeInterface $merchantFacade
     * @param \Pyz\Zed\SoftwarePackage\Persistence\SoftwarePackageQueryContainerInterface $softwarePackageQueryContainer
     * @param \Spryker\Zed\Acl\Business\AclFacadeInterface $aclFacade
     */
    public function __construct(
        MerchantFacadeInterface                $merchantFacade,
        SoftwarePackageQueryContainerInterface $softwarePackageQueryContainer,
        AclFacadeInterface                     $aclFacade
    )
    {
        $this->merchantFacade = $merchantFacade;
        $this->softwarePackageQueryContainer = $softwarePackageQueryContainer;
        $this->aclFacade = $aclFacade;
    }

    /**
     * @param $idMerchant
     * @return MerchantTransfer
     */
    public function getData($idMerchant = null) : MerchantTransfer
    {
        if($idMerchant === null){
            return new MerchantTransfer();
        }

        return $this
            ->merchantFacade
            ->getMerchantById($idMerchant);
    }

    /**
     * @return array
     */
    public function getOptions() : array
    {
        return [
            MerchantForm::OPTION_SALUTATION_CHOICES => $this->getSalutationSelectChoices(),
            MerchantForm::OPTION_SOFTWARE_PACKAGE_CHOICES => $this->getSoftwarePackageChoices(),
            MerchantForm::OPTION_ACL_GROUPS => $this->getAclGroupChoices()
        ];
    }

    /**
     * @return array
     */
    protected function getSalutationSelectChoices() : array
    {
        return array_combine(
            SpyMerchantTableMap::getValueSet(SpyMerchantTableMap::COL_SALUTATION),
            SpyMerchantTableMap::getValueSet(SpyMerchantTableMap::COL_SALUTATION)
        );
    }

    /**
     * @return array
     */
    protected function getSoftwarePackageChoices() : array
    {
        $softwarePackageEntities = $this
            ->softwarePackageQueryContainer
            ->querySoftwarePackage()
            ->find();

        $choices = [];
        foreach ($softwarePackageEntities as $softwarePackageEntity) {
            $choices[$softwarePackageEntity->getName()] = $softwarePackageEntity->getIdSoftwarePackage();
        }

        return $choices;
    }

    /**
     * @return array
     */
    protected function getAclGroupChoices(): array
    {
        $groups = $this
            ->aclFacade
            ->getAllGroups();

        $options = [];

        foreach ($groups->getGroups() as $group) {
            $options[$group->getName()] = $group
                ->getIdAclGroup();
        }

        return $options;
    }
}
