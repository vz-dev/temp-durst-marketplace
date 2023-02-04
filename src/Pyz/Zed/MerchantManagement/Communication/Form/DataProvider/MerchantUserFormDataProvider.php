<?php
/**
 * Durst - project - MerchantUserFormDataProvider.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 01.04.21
 * Time: 13:32
 */

namespace Pyz\Zed\MerchantManagement\Communication\Form\DataProvider;

use Generated\Shared\Transfer\MerchantUserTransfer;
use Orm\Zed\Merchant\Persistence\Map\DstMerchantUserTableMap;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface;
use Pyz\Zed\MerchantManagement\Communication\Form\MerchantUserUpdateForm;
use Spryker\Zed\Acl\Business\AclFacadeInterface;

class MerchantUserFormDataProvider
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
     * @var \Spryker\Zed\Acl\Business\AclFacadeInterface
     */
    protected $aclFacade;

    /**
     * MerchantUserFormDataProvider constructor.
     * @param \Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface $merchantQueryContainer
     * @param \Pyz\Zed\Merchant\Business\MerchantFacadeInterface $merchantFacade
     * @param \Spryker\Zed\Acl\Business\AclFacadeInterface $aclFacade
     */
    public function __construct(
        MerchantQueryContainerInterface $merchantQueryContainer,
        MerchantFacadeInterface         $merchantFacade,
        AclFacadeInterface              $aclFacade)
    {
        $this->merchantQueryContainer = $merchantQueryContainer;
        $this->merchantFacade = $merchantFacade;
        $this->aclFacade = $aclFacade;
    }

    /**
     * @param int|null $idMerchantUser
     * @return \Generated\Shared\Transfer\MerchantUserTransfer
     */
    public function getData(?int $idMerchantUser): MerchantUserTransfer
    {
        if ($idMerchantUser === null) {
            return new MerchantUserTransfer();
        }

        return $this
            ->merchantFacade
            ->getMerchantUserById(
                $idMerchantUser
            );
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return [
            MerchantUserUpdateForm::OPTION_ACL_GROUPS => $this->getAclGroupChoices(),
            MerchantUserUpdateForm::OPTION_MERCHANT_OPTIONS => $this->getMerchantOptions(),
            MerchantUserUpdateForm::OPTION_SALUTATION_OPTIONS => $this->getSalutationSelectChoices(),
        ];
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
            $options[$group->getName()] = $group->getIdAclGroup();
        }

        return $options;
    }

    /**
     * @return array
     */
    protected function getMerchantOptions(): array
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
    protected function getSalutationSelectChoices(): array
    {
        return array_combine(
            DstMerchantUserTableMap::getValueSet(DstMerchantUserTableMap::COL_SALUTATION),
            DstMerchantUserTableMap::getValueSet(DstMerchantUserTableMap::COL_SALUTATION)
        );
    }
}
