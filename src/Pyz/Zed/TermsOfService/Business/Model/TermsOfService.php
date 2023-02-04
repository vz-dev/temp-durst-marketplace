<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 11.01.18
 * Time: 15:43
 */

namespace Pyz\Zed\TermsOfService\Business\Model;


use Generated\Shared\Transfer\TermsOfServiceTransfer;
use Orm\Zed\TermsOfService\Persistence\SpyMerchantToTermsOfService;
use Orm\Zed\TermsOfService\Persistence\SpyTermsOfService;
use Propel\Runtime\ActiveQuery\Criteria;
use Pyz\Zed\TermsOfService\Business\Exception\NoCustomerTermsFound;
use Pyz\Zed\TermsOfService\Business\Exception\TermsOfServiceAlreadyAcceptedException;
use Pyz\Zed\TermsOfService\Business\Exception\TermsOfServiceNotFoundException;
use Pyz\Zed\TermsOfService\Persistence\TermsOfServiceQueryContainerInterface;
use Pyz\Zed\TermsOfService\TermsOfServiceConfig;

class TermsOfService
{
    const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    public const CONDITION_ACTIVE_UNTIL_NULL = 'CONDITION_ACTIVE_UNTIL_NULL';
    public const CONDITION_ACTIVE_UNTIL_AFTER_NOW = 'CONDITION_ACTIVE_UNTIL_AFTER_NOW';

    /**
     * @var TermsOfServiceQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var TermsOfServiceConfig
     */
    protected $config;

    /**
     * TermsOfService constructor.
     * @param TermsOfServiceQueryContainerInterface $queryContainer
     * @param TermsOfServiceConfig $config
     */
    public function __construct(
        TermsOfServiceQueryContainerInterface $queryContainer,
        TermsOfServiceConfig $config
    )
    {
        $this->queryContainer = $queryContainer;
        $this->config = $config;
    }


    /**
     * @param TermsOfServiceTransfer $transfer
     * @return TermsOfServiceTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function save(TermsOfServiceTransfer $transfer)
    {
        $entity = $this
            ->queryContainer
            ->queryTermsOfService()
            ->filterByIdTermsOfService($transfer->getIdTermsOfService())
            ->findOneOrCreate();

        $entity->fromArray($transfer->modifiedToArray());
        if($transfer->getActiveUntil() !== null){
            $entity->setActiveUntil(\DateTime::createFromFormat(self::DATE_TIME_FORMAT, $transfer->getActiveUntil()));
        }
        $entity->save();

        return $this
            ->entityToTransfer($entity);

    }

    /**
     * @param int $idTermsOfService
     * @return void
     * @throws TermsOfServiceNotFoundException if no terms of service object with the given
     * id can be found in the database
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function remove($idTermsOfService)
    {
        $entity = $this
            ->queryContainer
            ->queryTermsOfService()
            ->filterByIdTermsOfService( $idTermsOfService)
            ->findOne();

        if($entity === null){
            throw new TermsOfServiceNotFoundException(
                sprintf(
                    TermsOfServiceNotFoundException::NOT_FOUND_BY_ID,
                    $idTermsOfService
                )
            );
        }

        $entity->delete();
    }

    /**
     * @return bool
     */
    public function termsOfServiceAreImported()
    {
        return $this
            ->queryContainer
            ->queryTermsOfService()
            ->count() > 0;
    }

    /**
     * @param int $idTermsOfService
     * @return TermsOfServiceTransfer
     * @throws TermsOfServiceNotFoundException
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getTermsOfServiceById($idTermsOfService)
    {
        $entity = $this
            ->queryContainer
            ->queryTermsOfService()
            ->filterByIdTermsOfService($idTermsOfService)
            ->findOne();

        if($entity === null){
            throw new TermsOfServiceNotFoundException(
                sprintf(
                    TermsOfServiceNotFoundException::NOT_FOUND_BY_ID,
                    $idTermsOfService
                )
            );
        }

        return $this
            ->entityToTransfer($entity);
    }

    /**
     * @return TermsOfServiceTransfer[]
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getActiveTermsOfService()
    {
        $entities = $this
            ->queryContainer
            ->queryTermsOfService()
            ->filterByActiveUntil(time(), Criteria::LESS_THAN)
            ->find();

        $transfers = [];
        foreach($entities as $entity){
            $transfers[] = $this->entityToTransfer($entity);
        }

        return $transfers;
    }

    /**
     * @param SpyTermsOfService $entity
     * @return TermsOfServiceTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function entityToTransfer(SpyTermsOfService $entity)
    {
        $transfer = new TermsOfServiceTransfer;
        $transfer->fromArray($entity->toArray());
        if($entity->getActiveUntil() !== null) {
            $transfer
                ->setActiveUntil($entity->getActiveUntil()->format(self::DATE_TIME_FORMAT));
        }

        return $transfer;
    }

    /**
     * @param int $idMerchant
     * @return TermsOfServiceTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function getUnacceptedTermsOfServiceByIdMerchant($idMerchant)
    {
        $acceptedTermsOfService = $this
            ->queryContainer
            ->queryAcceptedTermsOfServiceByIdMerchant($idMerchant)
            ->find();

        $entity = $this
                ->queryContainer
                ->queryUnacceptedTermsOfService($acceptedTermsOfService, $this->config->getCustomerTermsName())
                ->findOne();

        return $this->entityToTransfer($entity);
    }

    /**
     * @param $idMerchant
     * @return bool
     */
    public function hasUnacceptedTermsOfServiceByIdMerchant($idMerchant)
    {
        $acceptedTermsOfService = $this
            ->queryContainer
            ->queryAcceptedTermsOfServiceByIdMerchant($idMerchant)
            ->find();

        return $this
            ->queryContainer
            ->queryUnacceptedTermsOfService($acceptedTermsOfService, $this->config->getCustomerTermsName())
            ->count() > 0;

    }

    /**
     * @param string $bundle
     * @param string $controller
     * @param string $action
     *
     * @return bool
     */
    public function isRouteIgnorable($bundle, $controller, $action)
    {
        $ignorable = $this->config->getTermsOfServiceIgnorable();
        foreach ($ignorable as $ignore) {
            if (($bundle === $ignore['bundle'] || $ignore['bundle'] === TermsOfServiceConfig::ROUTE_WILD_CARD) &&
                ($controller === $ignore['controller'] || $ignore['controller'] === TermsOfServiceConfig::ROUTE_WILD_CARD) &&
                ($action === $ignore['action'] || $ignore['action'] === TermsOfServiceConfig::ROUTE_WILD_CARD)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param int $idTermsOfService
     * @param int $idMerchant
     * @throws TermsOfServiceAlreadyAcceptedException
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function acceptTermsOfServiceByIdForMerchantById($idTermsOfService, $idMerchant)
    {
        if($this
            ->queryContainer
            ->queryMerchantToTermsOfService()
            ->filterByFkMerchant($idMerchant)
            ->filterByFkTermsOfService($idTermsOfService)
            ->count() > 0){
            throw new TermsOfServiceAlreadyAcceptedException(
                sprintf(
                    TermsOfServiceAlreadyAcceptedException::ALREADY_ACCEPTED,
                    $idTermsOfService,
                    $idMerchant
                )
            );
        }

        $entity = new SpyMerchantToTermsOfService();
        $entity->setFkMerchant($idMerchant);
        $entity->setFkTermsOfService($idTermsOfService);
        $entity->setAcceptedAt(time());
        $entity->save();
    }

    /**
     * @return TermsOfServiceTransfer
     * @throws NoCustomerTermsFound
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getCustomerTerms() : TermsOfServiceTransfer
    {
        $customerTermsName = $this
            ->config
            ->getCustomerTermsName();

        $entity = $this
            ->queryContainer
            ->queryActiveTermsOfService()
            ->filterByName($customerTermsName)
            ->findOne();

        if($entity === null){
            throw new NoCustomerTermsFound(
                sprintf(
                    NoCustomerTermsFound::MESSAGE,
                    $customerTermsName
                )
            );
        }

        return $this
            ->entityToTransfer($entity);
    }

    /**
     * @param int $timestamp
     * @return TermsOfServiceTransfer
     * @throws NoCustomerTermsFound
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getActiveCustomerTermsByTimestamp(int $timestamp) : TermsOfServiceTransfer
    {
        $customerTermsName = $this
            ->config
            ->getCustomerTermsName();

        $entity = $this
            ->queryContainer
            ->queryActiveCustomerTermsByTimestamp($timestamp, $customerTermsName)
            ->findOne();

        if($entity === null){
            throw new NoCustomerTermsFound(
                sprintf(
                    NoCustomerTermsFound::MESSAGE,
                    $customerTermsName
                )
            );
        }

        return $this
            ->entityToTransfer($entity);
    }
}
