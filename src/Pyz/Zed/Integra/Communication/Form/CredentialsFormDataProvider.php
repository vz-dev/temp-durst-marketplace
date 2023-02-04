<?php
/**
 * Durst - project - CredentialsFormDataProvider.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 06.11.20
 * Time: 09:46
 */

namespace Pyz\Zed\Integra\Communication\Form;

use Generated\Shared\Transfer\IntegraCredentialsTransfer;
use Orm\Zed\Integra\Persistence\Map\PyzIntegraCredentialsTableMap;
use Orm\Zed\Merchant\Persistence\Map\SpyBranchTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Pyz\Zed\Integra\Persistence\IntegraQueryContainerInterface;
use Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface;
use RuntimeException;

class CredentialsFormDataProvider
{
    /**
     * @var IntegraQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var MerchantQueryContainerInterface
     */
    protected $merchantQueryContainer;

    /**
     * CredentialsFormDataProvider constructor.
     *
     * @param IntegraQueryContainerInterface $queryContainer
     * @param MerchantQueryContainerInterface $merchantQueryContainer
     */
    public function __construct(
        IntegraQueryContainerInterface $queryContainer,
        MerchantQueryContainerInterface $merchantQueryContainer
    ) {
        $this->queryContainer = $queryContainer;
        $this->merchantQueryContainer = $merchantQueryContainer;
    }

    /**
     * @param int|null $idIntegraCredentials
     *
     * @throws RuntimeException
     *
     * @return IntegraCredentialsTransfer
     */
    public function getData(?int $idIntegraCredentials = null): IntegraCredentialsTransfer
    {
        if ($idIntegraCredentials === null) {
            return new IntegraCredentialsTransfer();
        }

        $entity = $this
            ->queryContainer
            ->queryIntegraCredentialsById($idIntegraCredentials)
            ->findOne();

        if ($entity === null) {
            throw new RuntimeException(sprintf('entity with id %d not found', $idIntegraCredentials));
        }

        return (new IntegraCredentialsTransfer())
            ->fromArray($entity->toArray());
    }

    /**
     * @param int|null $idIntegraCredentials
     *
     * @return array
     */
    public function getOptions(?int $idIntegraCredentials = null): array
    {
        return [
            CredentialsForm::OPTION_BRANCHES => $this->getBranchIds($idIntegraCredentials),
        ];
    }

    /**
     * @param int|null $idIntegraCredentials
     *
     * @return array
     */
    protected function getBranchIds(?int $idIntegraCredentials = null): array
    {
        $notAvailableBranchIds = $this
            ->queryContainer
            ->queryIntegraCredentials()
            ->select(PyzIntegraCredentialsTableMap::COL_FK_BRANCH);

        if ($idIntegraCredentials !== null) {
            $notAvailableBranchIds->filterByIdIntegraCredentials($idIntegraCredentials, Criteria::NOT_EQUAL);
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
