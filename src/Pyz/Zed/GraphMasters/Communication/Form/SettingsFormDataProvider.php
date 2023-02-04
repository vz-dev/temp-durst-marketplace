<?php
/**
 * Durst - project - SettingsFormDataProvider.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 27.05.21
 * Time: 19:46
 */

namespace Pyz\Zed\GraphMasters\Communication\Form;

use Generated\Shared\Transfer\GraphMastersSettingsTransfer;
use Orm\Zed\GraphMasters\Persistence\Map\DstGraphmastersSettingsTableMap;
use Orm\Zed\Merchant\Persistence\Map\SpyBranchTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Pyz\Zed\GraphMasters\Business\GraphMastersFacadeInterface;
use Pyz\Zed\GraphMasters\Persistence\GraphMastersQueryContainerInterface;
use Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface;
use RuntimeException;

class SettingsFormDataProvider
{
    /**
     * @var GraphMastersQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var MerchantQueryContainerInterface
     */
    protected $merchantQueryContainer;

    /**
     * @var GraphMastersFacadeInterface
     */
    protected $graphmastersFacade;

    /**
     * CredentialsFormDataProvider constructor.
     *
     * @param GraphMastersQueryContainerInterface $queryContainer
     * @param MerchantQueryContainerInterface $merchantQueryContainer
     * @param GraphMastersFacadeInterface $graphmastersFacade
     */
    public function __construct(
        GraphMastersQueryContainerInterface $queryContainer,
        MerchantQueryContainerInterface $merchantQueryContainer,
        GraphMastersFacadeInterface $graphmastersFacade
    ) {
        $this->queryContainer = $queryContainer;
        $this->merchantQueryContainer = $merchantQueryContainer;
        $this->graphmastersFacade = $graphmastersFacade;
    }

    /**
     * @param int|null $idSettings
     *
     * @throws RuntimeException
     *
     * @return GraphMastersSettingsTransfer
     */
    public function getData(?int $idSettings = null): GraphMastersSettingsTransfer
    {
        if ($idSettings === null) {
            return new GraphMastersSettingsTransfer();
        }

        return $this
            ->graphmastersFacade
            ->getSettingsById($idSettings, true);
    }

    /**
     * @param int|null $idIntegraCredentials
     *
     * @return array
     */
    public function getOptions(?int $idSettings = null): array
    {
        return [
            SettingsForm::OPTION_BRANCHES => $this->getBranchIds($idSettings),
        ];
    }

    /**
     * @param int|null $idSettings
     *
     * @return array
     */
    protected function getBranchIds(?int $idSettings = null): array
    {
        $notAvailableBranchIds = $this
            ->queryContainer
            ->queryGraphmastersSettings()
            ->select(DstGraphmastersSettingsTableMap::COL_FK_BRANCH);

        if ($idSettings !== null) {
            $notAvailableBranchIds->filterByIdGraphmastersSettings($idSettings, Criteria::NOT_EQUAL);
        }

        $branches = $this
            ->merchantQueryContainer
            ->queryBranch()
            ->filterByIdBranch($notAvailableBranchIds, Criteria::NOT_IN)
            ->select([
                SpyBranchTableMap::COL_ID_BRANCH,
                SpyBranchTableMap::COL_NAME,
            ])
            ->find();

        $branchOptions = [];
        foreach ($branches as $branch) {
            $branchOptions[$branch[SpyBranchTableMap::COL_NAME]] = $branch[SpyBranchTableMap::COL_ID_BRANCH];
        }

        return $branchOptions;
    }
}
