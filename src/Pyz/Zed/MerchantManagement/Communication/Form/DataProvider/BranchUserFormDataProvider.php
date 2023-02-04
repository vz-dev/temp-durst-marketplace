<?php
/**
 * Durst - project - BranchUserFormDataProvider.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 06.02.20
 * Time: 10:45
 */

namespace Pyz\Zed\MerchantManagement\Communication\Form\DataProvider;

use Generated\Shared\Transfer\BranchUserTransfer;
use Orm\Zed\Merchant\Persistence\Map\DstBranchUserTableMap;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface;
use Pyz\Zed\MerchantManagement\Communication\Form\BranchUserUpdateForm;
use Spryker\Zed\Acl\Business\AclFacadeInterface;

class BranchUserFormDataProvider
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
     * BranchUserFormDataProvider constructor.
     * @param MerchantQueryContainerInterface $merchantQueryContainer
     * @param MerchantFacadeInterface $merchantFacade
     * @param \Spryker\Zed\Acl\Business\AclFacadeInterface $aclFacade
     */
    public function __construct(
        MerchantQueryContainerInterface $merchantQueryContainer,
        MerchantFacadeInterface         $merchantFacade,
        AclFacadeInterface              $aclFacade
    )
    {
        $this->merchantQueryContainer = $merchantQueryContainer;
        $this->merchantFacade = $merchantFacade;
        $this->aclFacade = $aclFacade;
    }

    /**
     * @param int|null $idBranchUser
     * @return \Generated\Shared\Transfer\BranchUserTransfer
     */
    public function getData(?int $idBranchUser): BranchUserTransfer
    {
        if ($idBranchUser === null) {
            return new BranchUserTransfer();
        }

        return $this
            ->merchantFacade
            ->getBranchUserById($idBranchUser);
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return [
            BranchUserUpdateForm::OPTION_SALUTATION_OPTIONS => $this->getSalutationSelectChoices(),
            BranchUserUpdateForm::OPTION_BRANCH_OPTIONS => $this->getBranchOptions(),
            BranchUserUpdateForm::OPTION_ACL_GROUPS => $this->getAclGroupChoices()
        ];
    }

    /**
     * @return array
     */
    protected function getBranchOptions(): array
    {
        $branchEntities = $this
            ->merchantQueryContainer
            ->queryBranch()
            ->find();

        $options = [];

        foreach ($branchEntities as $branchEntity) {
            $options[$branchEntity->getName()] = $branchEntity->getIdBranch();
        }

        return $options;
    }

    /**
     * @return array
     */
    protected function getSalutationSelectChoices(): array
    {
        return array_combine(
            DstBranchUserTableMap::getValueSet(DstBranchUserTableMap::COL_SALUTATION),
            DstBranchUserTableMap::getValueSet(DstBranchUserTableMap::COL_SALUTATION)
        );
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
}
