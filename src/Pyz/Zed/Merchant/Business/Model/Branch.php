<?php
/**
 * Durst - project - Branch.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 15.03.21
 * Time: 10:46
 */

namespace Pyz\Zed\Merchant\Business\Model;

use ArrayObject;
use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\PaymentMethodTransfer;
use Orm\Zed\DeliveryArea\Persistence\Map\SpyTimeSlotTableMap;
use Orm\Zed\Merchant\Persistence\Map\SpyBranchTableMap;
use Orm\Zed\Merchant\Persistence\SpyBranch;
use Propel\Runtime\Exception\PropelException;
use Pyz\Shared\MerchantAuth\MerchantAuthConstants;
use Pyz\Zed\DeliveryArea\Business\DeliveryAreaFacadeInterface;
use Pyz\Zed\DeliveryArea\Business\Exception\DeliveryAreaNotFoundException;
use Pyz\Zed\GraphMasters\Business\GraphMastersFacadeInterface;
use Pyz\Zed\Merchant\Business\Code\CodeGeneratorInterface;
use Pyz\Zed\Merchant\Business\Code\GlnValidatorInterface;
use Pyz\Zed\Merchant\Business\Exception\BranchInactiveException;
use Pyz\Zed\Merchant\Business\Exception\BranchNotFoundException;
use Pyz\Zed\Merchant\Business\Exception\BranchPriceModeInvalidException;
use Pyz\Zed\Merchant\Business\Exception\BranchStatusInvalidException;
use Pyz\Zed\Merchant\Business\Exception\Code\CodeExistsException;
use Pyz\Zed\Merchant\Business\Exception\Code\CodeMalformedException;
use Pyz\Zed\Merchant\Business\Exception\PaymentMethodNotFoundException;
use Pyz\Zed\Merchant\Business\Exception\SalutationNotFoundException;
use Pyz\Zed\Merchant\Communication\Plugin\BranchHydratorPluginInterface;
use Pyz\Zed\Merchant\Communication\Plugin\BranchPostRemovePluginInterface;
use Pyz\Zed\Merchant\Communication\Plugin\BranchPostSaverPluginInterface;
use Pyz\Zed\Merchant\Communication\Plugin\BranchPreSaverPluginInterface;
use Pyz\Zed\Merchant\MerchantConfig;
use Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Branch implements BranchInterface
{
    //TODO Refactor
    public const BRANCH_SESSION_KEY = 'branch';
    public const PAYMENT_METHOD_IDS_KEY_B2B = 'b2b';
    public const PAYMENT_METHOD_IDS_KEY_B2C = 'b2c';

    /**
     * @var MerchantQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var DeliveryAreaFacadeInterface
     */
    protected $deliveryAreaFacade;

    /**
     * @var MerchantConfig
     */
    protected $config;

    /**
     * @var CodeGeneratorInterface
     */
    protected $codeGenerator;

    /**
     * @var array|BranchHydratorPluginInterface[]
     */
    protected $hydratorPlugins;

    /**
     * @var array|BranchPostSaverPluginInterface[]
     */
    protected $saverPlugins;

    /**
     * @var array|BranchPreSaverPluginInterface[]
     */
    protected $preSavePlugins;

    /**
     * @var array|BranchPostRemovePluginInterface[]
     */
    protected $removePlugins;

    /**
     * @var array|BranchPostSaverPluginInterface[]
     */
    protected $statusIndependentSaverPlugins;

    /**
     * @var GlnValidatorInterface
     */
    protected $glnValidator;

    /**
     * @var GraphMastersFacadeInterface
     */
    protected $graphMastersFacade;

    /**
     * @param MerchantQueryContainerInterface $queryContainer
     * @param SessionInterface $session
     * @param DeliveryAreaFacadeInterface $deliveryAreaFacade
     * @param MerchantConfig $config
     * @param CodeGeneratorInterface $codeGenerator
     * @param array $hydratorPlugins
     * @param array $saverPlugins
     * @param array $preSavePlugins
     * @param array $removePlugins
     * @param array $statusIndependentSaverPlugins
     * @param GlnValidatorInterface $glnValidator
     * @param GraphMastersFacadeInterface $graphMastersFacade
     */
    public function __construct(
        MerchantQueryContainerInterface $queryContainer,
        SessionInterface             $session,
        DeliveryAreaFacadeInterface  $deliveryAreaFacade,
        MerchantConfig                  $config,
        CodeGeneratorInterface       $codeGenerator,
        array                        $hydratorPlugins,
        array                        $saverPlugins,
        array                        $preSavePlugins,
        array                        $removePlugins,
        array                        $statusIndependentSaverPlugins,
        GlnValidatorInterface        $glnValidator,
        GraphMastersFacadeInterface  $graphMastersFacade
    )
    {
        $this->queryContainer = $queryContainer;
        $this->session = $session;
        $this->deliveryAreaFacade = $deliveryAreaFacade;
        $this->config = $config;
        $this->codeGenerator = $codeGenerator;
        $this->hydratorPlugins = $hydratorPlugins;
        $this->saverPlugins = $saverPlugins;
        $this->preSavePlugins = $preSavePlugins;
        $this->removePlugins = $removePlugins;
        $this->statusIndependentSaverPlugins = $statusIndependentSaverPlugins;
        $this->glnValidator = $glnValidator;
        $this->graphMastersFacade = $graphMastersFacade;
    }

    /**
     * @param BranchTransfer $branchTransfer
     * @return BranchTransfer
     * @throws BranchNotFoundException
     * @throws BranchStatusInvalidException
     * @throws PaymentMethodNotFoundException
     * @throws SalutationNotFoundException
     * @throws CodeExistsException
     * @throws CodeMalformedException
     * @throws PropelException
     * @throws AmbiguousComparisonException
     */
    public function save(BranchTransfer $branchTransfer): BranchTransfer
    {
        //TODO make two different methods one for adding, one for updating
        if ($branchTransfer->getIdBranch() !== null) {
            $branchEntity = $this->getEntityBranchById($branchTransfer->getIdBranch());
        } else {
            $branchEntity = new SpyBranch();
        }

        $branchEntity->setFkMerchant($branchTransfer->getFkMerchant());
        $branchEntity->setName($branchTransfer->getName());
        if ($branchTransfer->getDefaultMinValueFirst() !== null) {
            $branchEntity->setDefaultMinValueFirst($branchTransfer->getDefaultMinValueFirst());
        }

        if ($branchTransfer->getDefaultMinValueFollowing() !== null) {
            $branchEntity->setDefaultMinValueFollowing($branchTransfer->getDefaultMinValueFollowing());
        }

        if ($branchTransfer->getDefaultDeliveryCosts() !== null) {
            $branchEntity->setDefaultDeliveryCosts($branchTransfer->getDefaultDeliveryCosts());
        }

        if ($branchTransfer->getContactPersonSalutationId() !== null) {
            if ($this
                    ->hasSalutationById($branchTransfer->getContactPersonSalutationId()) !== true) {
                throw new SalutationNotFoundException(
                    sprintf(
                        SalutationNotFoundException::ID_NOT_FOUND,
                        $branchTransfer->getContactPersonSalutationId()
                    )
                );
            }
            $branchEntity->setFkContactPersonSalutation($branchTransfer->getContactPersonSalutationId());
        }

        if ($branchTransfer->getContactPersonName() !== null) {
            $branchEntity->setContactPersonName($branchTransfer->getContactPersonName());
        }

        if ($branchTransfer->getContactPersonPreName() !== null) {
            $branchEntity->setContactPersonPreName($branchTransfer->getContactPersonPreName());
        }

        if ($branchTransfer->getCompanyProfile() !== null) {
            $branchEntity->setCompanyProfile($branchTransfer->getCompanyProfile());
        }

        if ($branchTransfer->getCity() !== null) {
            $branchEntity->setCity($branchTransfer->getCity());
        }

        if ($branchTransfer->getZip() !== null) {
            $branchEntity->setZip($branchTransfer->getZip());
        }

        if ($branchTransfer->getStreet() !== null) {
            $branchEntity->setStreet($branchTransfer->getStreet());
        }

        if ($branchTransfer->getNumber() !== null) {
            $branchEntity->setNumber($branchTransfer->getNumber());
        }

        if ($branchTransfer->getEmail() !== null) {
            $branchEntity->setEmail($branchTransfer->getEmail());
        }

        if ($branchTransfer->getLogoUrlLarge() !== null) {
            $branchEntity->setImageUrlLarge($branchTransfer->getLogoUrlLarge());
        }

        if ($branchTransfer->getLogoUrlSmall() !== null) {
            $branchEntity->setImageUrlSmall($branchTransfer->getLogoUrlSmall());
        }

        if ($branchTransfer->getTermsOfService() !== null) {
            $branchEntity->setTermsOfService($branchTransfer->getTermsOfService());
        }

        if ($branchTransfer->getCompanyProfile() !== null) {
            $branchEntity->setCompanyProfile($branchTransfer->getCompanyProfile());
        }

        if ($branchTransfer->getPhone() !== null) {
            $branchEntity->setPhone($branchTransfer->getPhone());
        }

        if ($branchTransfer->getCorporateName() !== null) {
            $branchEntity->setCorporateName($branchTransfer->getCorporateName());
        }

        if ($branchTransfer->getGln() !== null) {
            $branchEntity->setGln($branchTransfer->getGln());
        }

        if ($branchTransfer->getDurstGln() !== null) {
            $branchEntity->setDurstGln($branchTransfer->getDurstGln());
        }

        if ($branchTransfer->getSumupAffiliateKey() !== null) {
            $branchEntity->setSumupAffiliateKey($branchTransfer->getSumupAffiliateKey());
        }

        if ($branchTransfer->getDataRetentionDays() !== null) {
            $branchEntity->setDataRetentionDays($branchTransfer->getDataRetentionDays());
        }

        $branchEntity->setDispatcherName($branchTransfer->getDispatcherName());
        $branchEntity->setDispatcherEmail($branchTransfer->getDispatcherEmail());
        $branchEntity->setDispatcherPhone($branchTransfer->getDispatcherPhone());
        $branchEntity->setAccountingName($branchTransfer->getAccountingName());
        $branchEntity->setAccountingEmail($branchTransfer->getAccountingEmail());
        $branchEntity->setAccountingPhone($branchTransfer->getAccountingPhone());
        $branchEntity->setServiceName($branchTransfer->getServiceName());
        $branchEntity->setServiceEmail($branchTransfer->getServiceEmail());
        $branchEntity->setServicePhone($branchTransfer->getServicePhone());
        $branchEntity->setMarketingName($branchTransfer->getMarketingName());
        $branchEntity->setMarketingEmail($branchTransfer->getMarketingEmail());
        $branchEntity->setMarketingPhone($branchTransfer->getMarketingPhone());
        $branchEntity->setEdiEndpointUrl($branchTransfer->getEdiEndpointUrl());
        $branchEntity->setEdiDepositEndpointUrl($branchTransfer->getEdiDepositEndpointUrl());
        $branchEntity->setAccessToken($branchTransfer->getAccessToken());
        $branchEntity->setBasicAuthUsername($branchTransfer->getBasicAuthUsername());
        $branchEntity->setBasicAuthPassword($branchTransfer->getBasicAuthPassword());
        $branchEntity->setAutoEdiExport($branchTransfer->getAutoEdiExport());
        $branchEntity->setWarehouseLat($branchTransfer->getWarehouseLat());
        $branchEntity->setWarehouseLng($branchTransfer->getWarehouseLng());
        $branchEntity->setHeidelpayPrivateKey($this->encryptHeidelpayPrivateKey($branchTransfer->getHeidelpayPrivateKey()));
        $branchEntity->setHeidelpayPublicKey($branchTransfer->getHeidelpayPublicKey());
        $branchEntity->setBillingCompany($branchTransfer->getBillingCompany());
        $branchEntity->setBillingStreet($branchTransfer->getBillingStreet());
        $branchEntity->setBillingNumber($branchTransfer->getBillingNumber());
        $branchEntity->setBillingZip($branchTransfer->getBillingZip());
        $branchEntity->setBillingCity($branchTransfer->getBillingCity());
        $branchEntity->setSalesTaxId($branchTransfer->getSalesTaxId());
        $branchEntity->setPlaceJurisdiction($branchTransfer->getPlaceJurisdiction());
        $branchEntity->setEcoControlNumber($branchTransfer->getEcoControlNumber());
        $branchEntity->setPersonResponsible($branchTransfer->getPersonResponsible());
        $branchEntity->setBillingEmail($branchTransfer->getBillingEmail());

        switch ($branchTransfer->getStatus()) {
            case SpyBranchTableMap::COL_STATUS_ACTIVE:
                // intentionally fall through
            case SpyBranchTableMap::COL_STATUS_BLOCKED:
                // intentionally fall through
            case SpyBranchTableMap::COL_STATUS_DELETED:
                $branchEntity->setStatus($branchTransfer->getStatus());
                break;
            case null:
                // do nothing
                break;
            default:
                throw new BranchStatusInvalidException(
                    BranchStatusInvalidException::MESSAGE
                );
        }

        switch ($branchTransfer->getPriceMode()) {
            case SpyBranchTableMap::COL_PRICE_MODE_NET_MODE:
                $branchEntity->setPriceMode($branchTransfer->getPriceMode());
            case SpyBranchTableMap::COL_PRICE_MODE_GROSS_MODE:
                $branchEntity->setPriceMode($branchTransfer->getPriceMode());
            case null:
                // do nothing
                break;
            default:
                throw new BranchPriceModeInvalidException(
                    BranchPriceModeInvalidException::MESSAGE
                );
        }

        if ($branchEntity->isNew()) {
            if ($branchTransfer->getCode() !== null &&
                $this->codeGenerator->checkCode($branchTransfer->getCode()) === true) {
                $branchEntity->setCode($branchTransfer->getCode());
            } else {
                $branchEntity->setCode($this->codeGenerator->generateCode());
            }
            $unitsOrderedCount = 0;
            $branchEntity->setUnitsOrderedCount($unitsOrderedCount);
        }

        $this->runBranchPreSavePlugins($branchEntity, $branchTransfer);

        $branchEntity->save();

        $paymentMethodIdsByType = [
            self::PAYMENT_METHOD_IDS_KEY_B2B => $branchTransfer->getB2bPaymentMethodIds(),
            self::PAYMENT_METHOD_IDS_KEY_B2C => $branchTransfer->getB2cPaymentMethodIds()
        ];

        $paymentMethodIds = array_unique(array_merge($branchTransfer->getB2cPaymentMethodIds(), $branchTransfer->getB2bPaymentMethodIds()));
        if (empty($paymentMethodIds) !== true
        ) {
            foreach ($paymentMethodIds as $idPaymentMethod) {
                if ($this->hasPaymentMethodById($idPaymentMethod) !== true) {
                    throw new PaymentMethodNotFoundException(
                        sprintf(
                            PaymentMethodNotFoundException::NOT_FOUND,
                            $idPaymentMethod
                        )
                    );
                }

                $paymentEntity = $this
                    ->queryContainer
                    ->queryBranchToPaymentMethod()
                    ->filterByFkBranch($branchTransfer->getIdBranch())
                    ->filterByFkPaymentMethod($idPaymentMethod)
                    ->findOneOrCreate();

                if ($paymentEntity->getB2c() !== in_array($idPaymentMethod, $paymentMethodIdsByType['b2c'])) {
                    $paymentEntity->setB2c(in_array($idPaymentMethod, $paymentMethodIdsByType['b2c']));
                    $paymentEntity->save();
                }

                if ($paymentEntity->getB2b() !== in_array($idPaymentMethod, $paymentMethodIdsByType['b2b'])) {
                    $paymentEntity->setB2b(in_array($idPaymentMethod, $paymentMethodIdsByType['b2b']));
                    $paymentEntity->save();
                }
            }
        }

        $this->runPluginsBasedOnBranchStatus($branchEntity, $branchTransfer);
        $this->runStatusIndependentSaverPlugins($branchEntity, $branchTransfer);

        return $this->entityToTransfer($branchEntity, $paymentMethodIdsByType);
    }

    /**
     * @param string $code
     * @return BranchTransfer
     * @throws BranchInactiveException
     * @throws BranchNotFoundException
     * @throws PropelException
     * @throws AmbiguousComparisonException
     */
    public function getBranchByCode(string $code): BranchTransfer
    {
        $entity = $this
            ->queryContainer
            ->queryBranchByCode($code)
            ->findOne();

        if ($entity === null) {
            throw new BranchNotFoundException(
                sprintf(
                    BranchNotFoundException::CODE_NOT_FOUND,
                    $code
                )
            );
        }

        if ($entity->getStatus() !== SpyBranchTableMap::COL_STATUS_ACTIVE) {
            throw new BranchInactiveException(
                sprintf(
                    BranchInactiveException::MESSAGE,
                    $code
                )
            );
        }

        $paymentMethods = [];
        foreach ($entity->getSpyBranchToPaymentMethods() as $paymentMethod) {
            $paymentMethods[] = (new PaymentMethodTransfer())
                ->fromArray($paymentMethod->getSpyPaymentMethod()->toArray(), true)
                ->setB2b($paymentMethod->getB2b())
                ->setB2c($paymentMethod->getB2c());
        }

        return $this
            ->entityToTransfer(
                $entity,
                null,
                $paymentMethods
            );
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasBranchByName(string $name): bool
    {
        $amount = $this->queryContainer->queryBranchByName($name)->count();

        return $amount > 0;
    }

    /**
     * @param int $idBranch
     * @return bool
     */
    public function hasBranchById(int $idBranch): bool
    {
        $amount = $this->queryContainer->queryBranchById($idBranch)->count();

        return $amount > 0;
    }

    /**
     * @param int $idBranch
     * @return BranchTransfer
     * @throws BranchNotFoundException
     * @throws PropelException
     * @throws AmbiguousComparisonException
     */
    public function getBranchById(int $idBranch): BranchTransfer
    {
        $entity = $this
            ->queryContainer
            ->queryBranchById($idBranch)
            ->findOne();

        if ($entity === null) {
            throw new BranchNotFoundException();
        }

        $paymentMethods = $this
            ->queryContainer
            ->queryBranchToPaymentMethod()
            ->filterByFkBranch($entity->getIdBranch())
            ->find();

        $paymentMethodIds = [
            self::PAYMENT_METHOD_IDS_KEY_B2B => [],
            self::PAYMENT_METHOD_IDS_KEY_B2C => []
        ];
        foreach ($paymentMethods as $paymentMethod) {
            if ($paymentMethod->getB2b()) {
                $paymentMethodIds[self::PAYMENT_METHOD_IDS_KEY_B2B][] = $paymentMethod->getFkPaymentMethod();
            }

            if ($paymentMethod->getB2c()) {
                $paymentMethodIds[self::PAYMENT_METHOD_IDS_KEY_B2C][] = $paymentMethod->getFkPaymentMethod();
            }
        }

        return $this->entityToTransfer($entity, $paymentMethodIds);
    }

    /**
     * @param int $idBranch
     * @param int $orderedUnits
     * @return void
     * @throws PropelException
     * @throws BranchNotFoundException
     */
    public function sumUpOrderedUnitsToBranchById(int $idBranch, int $orderedUnits): void
    {
        $entity = $this
            ->queryContainer
            ->queryBranchById($idBranch)
            ->findOne();

        if ($entity === null) {
            throw new BranchNotFoundException();
        }

        $unitsOrderedCount = $entity->getUnitsOrderedCount();
        if ($unitsOrderedCount === null) {
            $unitsOrderedCount = 0;
        }
        $unitsOrderedCount += $orderedUnits;

        $entity->setUnitsOrderedCount($unitsOrderedCount);
        $entity->save();
    }

    /**
     * @param int $idMerchant
     * @return array
     * @throws PropelException
     */
    public function getBranchesByIdMerchant(int $idMerchant): array
    {
        $branches = [];
        $branchEntityList = $this->queryContainer->queryBranchByIdMerchant($idMerchant)->find();
        foreach ($branchEntityList as $branchEntity) {
            $branches[] = $this->entityToTransfer($branchEntity);
        }
        return $branches;
    }

    /**
     * @param int $idMerchant
     * @return BranchTransfer
     * @throws BranchNotFoundException
     * @throws PropelException
     */
    public function getBranchByIdMerchant(int $idMerchant): BranchTransfer
    {
        $branchEntity = $this->queryContainer->queryBranchByIdMerchant($idMerchant)->findOne();
        if ($branchEntity === null) {
            throw new BranchNotFoundException();
        }
        return $this->entityToTransfer($branchEntity);
    }

    /**
     * @return bool
     */
    public function hasCurrentBranch(): bool
    {
        $branch = $this->readNewBranchFromSession();

        return $branch !== null;
    }

    /**
     * @param BranchTransfer $branch
     * @return mixed
     */
    public function setCurrentBranch(BranchTransfer $branch)
    {
        $key = $this->createBranchKey();

        return $this->session->set($key, clone $branch);
    }

    /**
     * @return BranchTransfer|null
     * @throws BranchNotFoundException
     */
    public function getCurrentBranch(): ?BranchTransfer
    {
        $branch = $this->readNewBranchFromSession();

        if ($branch === null) {
            throw new BranchNotFoundException();
        }

        return clone $branch;
    }

    /**
     * @return void
     */
    public function logout(): void
    {
        $key = $this->createBranchKey();
        if ($this->session->has($key)) {
            $this->session->remove($key);
            $this->session->migrate();
        }
    }

    /**
     * @param string $bundle
     * @param string $controller
     * @param string $action
     *
     * @return bool
     */
    public function isIgnorablePath(string $bundle, string $controller, string $action): bool
    {
        //@TODO remove dependency to MerchantAuthConstants
        $ignorable = $this->config->getBranchIgnorable();
        foreach ($ignorable as $ignore) {
            if (($bundle === $ignore['bundle'] || $ignore['bundle'] === MerchantAuthConstants::AUTHORIZATION_WILDCARD) &&
                ($controller === $ignore['controller'] || $ignore['controller'] === MerchantAuthConstants::AUTHORIZATION_WILDCARD) &&
                ($action === $ignore['action'] || $ignore['action'] === MerchantAuthConstants::AUTHORIZATION_WILDCARD)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function branchesAreImported(): bool
    {
        return $this->queryContainer->queryBranch()->count() > 0;
    }

    /**
     * @return BranchTransfer[]
     * @throws PropelException
     */
    public function getBranches(): array
    {
        $entities = $this
            ->queryContainer
            ->queryBranchNotDeleted()
            ->find();

        $transfers = [];
        foreach ($entities as $entity) {
            $transfers[] = $this->entityToTransfer($entity);
        }

        return $transfers;
    }

    /**
     * @param string $zipCode
     * @return BranchTransfer[]
     * @throws PropelException
     * @throws DeliveryAreaNotFoundException
     * @throws AmbiguousComparisonException
     */
    public function getBranchesByZipCode(string $zipCode): array
    {
        $entities = $this
            ->queryContainer
            ->queryBranch()
            ->filterByStatus(SpyBranchTableMap::COL_STATUS_ACTIVE)
            ->useSpyTimeSlotQuery()
            ->filterByIsActive(true)
            ->filterByStatus(SpyTimeSlotTableMap::COL_STATUS_ACTIVE)
            ->useSpyDeliveryAreaQuery()
            ->filterByZipCode($zipCode)
            ->endUse()
            ->endUse()
            ->groupByIdBranch()
            ->find();

        // if we can't find anything see if there is a active graphmaster category
        if($entities->count() === 0)
        {
            $entities = $this
                ->queryContainer
                ->queryBranch()
                ->filterByStatus(SpyBranchTableMap::COL_STATUS_ACTIVE)
                ->useDstGraphmastersDeliveryAreaCategoryQuery()
                    ->useDstGraphmastersDeliveryAreaCategoryToDeliveryAreaQuery()
                        ->useSpyDeliveryAreaQuery()
                            ->filterByZipCode($zipCode)
                        ->endUse()
                    ->endUse()
                    ->filterByIsActive(true)
                ->endUse()
                ->groupByIdBranch()
                ->find();
        }

        $transfers = [];
        foreach ($entities as $entity) {
            $paymentMethods = [];
            foreach ($entity->getSpyBranchToPaymentMethods() as $paymentMethod) {
                $paymentMethods[] = (new PaymentMethodTransfer())
                    ->fromArray($paymentMethod->getSpyPaymentMethod()->toArray(), true)
                    ->setB2b($paymentMethod->getB2b())
                    ->setB2c($paymentMethod->getB2c());
            }

            $transfers[] = $this
                ->entityToTransfer($entity, null, $paymentMethods);
        }

        return $transfers;
    }

    /**
     * @param int $idBranch
     * @return void
     * @throws BranchNotFoundException
     * @throws PropelException
     */
    public function deleteBranch(int $idBranch): void
    {
        $branchEntity = $this
            ->getEntityBranchById($idBranch);

        $branchEntity->setStatus('deleted');

        $branchEntity->save();

        $this->runRemovePlugins($branchEntity, $this->entityToTransfer($branchEntity));
        $this->runStatusIndependentSaverPlugins($branchEntity, $this->entityToTransfer($branchEntity));
    }

    /**
     * @param int $id
     * @param string $zipCode
     * @return BranchTransfer|null
     * @throws PropelException
     * @throws AmbiguousComparisonException
     */
    public function getBranchByIdAndZipCode(int $id, string $zipCode): ?BranchTransfer
    {
        $entity = $this
            ->queryContainer
            ->queryBranchById($id)
            ->filterByStatus(SpyBranchTableMap::COL_STATUS_ACTIVE)
            ->useSpyTimeSlotQuery()
            ->useSpyDeliveryAreaQuery()
            ->filterByZipCode($zipCode)
            ->endUse()
            ->filterByIsActive(true)
            ->filterByStatus(SpyTimeSlotTableMap::COL_STATUS_ACTIVE)
            ->endUse()
            ->findOne();

        if ($entity === null) {
            return null;
        }

        $paymentMethods = [];
        foreach ($entity->getSpyBranchToPaymentMethods() as $paymentMethod) {
            $paymentMethods[] = (new PaymentMethodTransfer())
                ->fromArray($paymentMethod->getSpyPaymentMethod()->toArray(), true)
                ->setB2b($paymentMethod->getB2b())
                ->setB2c($paymentMethod->getB2c());
        }

        return $this
            ->entityToTransfer($entity, null, $paymentMethods);
    }

    /**
     * @param int $idBranch
     * @return void
     * @throws BranchNotFoundException
     * @throws PropelException
     */
    public function restoreBranch(int $idBranch): void
    {
        $branchEntity = $this
            ->getEntityBranchById($idBranch);

        if ($branchEntity->getStatus() !== SpyBranchTableMap::COL_STATUS_DELETED) {
            return;
        }

        $branchEntity->setStatus(SpyBranchTableMap::COL_STATUS_BLOCKED);

        $branchEntity->save();
    }

    /**
     * @return void
     * @throws BranchNotFoundException
     * @throws PropelException
     */
    public function activateCurrentBranch(): void
    {
        $this->changeStatusForCurrentBranch(SpyBranchTableMap::COL_STATUS_ACTIVE);
    }

    /**
     * @return void
     * @throws BranchNotFoundException
     * @throws PropelException
     */
    public function deactivateCurrentBranch(): void
    {
        $this->changeStatusForCurrentBranch(SpyBranchTableMap::COL_STATUS_BLOCKED);
    }

    /**
     * @param string $hash
     * @return BranchTransfer
     * @throws BranchNotFoundException
     * @throws PropelException
     */
    public function getBranchByHash(string $hash): BranchTransfer
    {
        $branchEntities = $this->queryContainer->queryBranch()->find();

        foreach ($branchEntities as $branchEntity) {
            if ($hash === $this->getHashForBranch($this->entityToTransfer($branchEntity))) {
                return $this->entityToTransfer($branchEntity);
            }
        }

        throw new BranchNotFoundException(
            sprintf(
                BranchNotFoundException::HASH_NOT_FOUND,
                $hash
            )
        );
    }

    /**
     * @param BranchTransfer $branchTransfer
     * @return string
     */
    public function getHashForBranch(BranchTransfer $branchTransfer): string
    {
        $hashString = sprintf('%s%s%s%s%s',
            $branchTransfer->getFkMerchant(),
            $branchTransfer->getName(),
            $branchTransfer->getIdBranch(),
            $branchTransfer->getEmail(),
            $this->config->getBranchHashSalt()
        );

        return md5($hashString);
    }

    /**
     * @param int $idBranch
     * @param int $licenseUnits
     * @return void
     * @throws PropelException
     * @throws BranchNotFoundException
     */
    public function sumUpLicenseUnitsToBranchById(int $idBranch, int $licenseUnits): void
    {
        $entity = $this
            ->queryContainer
            ->queryBranchById($idBranch)
            ->findOne();

        if ($entity === null) {
            throw new BranchNotFoundException();
        }

        $unitsLicenseCount = $entity->getUnitsLicenseCount();

        if ($unitsLicenseCount === null) {
            $unitsLicenseCount = 0;
        }

        $unitsLicenseCount += $licenseUnits;

        $entity->setUnitsLicenseCount($unitsLicenseCount);
        $entity->save();
    }

    /**
     * @param SpyBranch $branchEntity
     * @param int[] $paymentMethodIds
     * @param array $paymentMethods
     * @return BranchTransfer
     * @throws PropelException
     */
    protected function entityToTransfer(SpyBranch $branchEntity, $paymentMethodIds = [], $paymentMethods = []): BranchTransfer
    {
        $branchTransfer = new BranchTransfer();
        $branchTransfer->fromArray($branchEntity->toArray(), true);
        $branchTransfer->setLogoUrlLarge($branchEntity->getImageUrlLarge());
        $branchTransfer->setLogoUrlSmall($branchEntity->getImageUrlSmall());

        if ($branchEntity->getSpyEnumSalutation() !== null) {
            $branchTransfer->setContactPersonSalutationId($branchEntity->getSpyEnumSalutation()->getIdEnumSalutation());
        }

        if ($branchEntity->getPriceMode() !== null) {
            $branchTransfer->setPriceMode($branchEntity->getPriceMode());
        }

        if (empty($paymentMethodIds) !== true) {
            $branchTransfer->setPaymentMethodIds(array_merge($paymentMethodIds[self::PAYMENT_METHOD_IDS_KEY_B2C], $paymentMethodIds[self::PAYMENT_METHOD_IDS_KEY_B2B]));
            $branchTransfer->setB2cPaymentMethodIds($paymentMethodIds[self::PAYMENT_METHOD_IDS_KEY_B2C]);
            $branchTransfer->setB2bPaymentMethodIds($paymentMethodIds[self::PAYMENT_METHOD_IDS_KEY_B2B]);
        }
        $branchTransfer->setPaymentMethods(new ArrayObject($paymentMethods));
        $branchTransfer->setUnitsOrderedCount($branchEntity->getUnitsOrderedCount());
        $branchTransfer->setCodeSoftwarePackage($branchEntity->getSpyMerchant()->getDstSoftwarePackage()->getCode());
        $branchTransfer->setHeidelpayPrivateKey($this->decryptHeidelpayPrivateKey($branchEntity->getHeidelpayPrivateKey()));

        $branchTransfer->setUsesGraphmasters(
            $this
                ->graphMastersFacade
                ->doesBranchUseGraphmasters(
                    $branchEntity
                        ->getIdBranch()
                )
        );

        $this->hydrateBranch($branchEntity, $branchTransfer);

        return $branchTransfer;
    }

    /**
     * @param SpyBranch $branchEntity
     * @param BranchTransfer $branchTransfer
     * @return void
     */
    protected function hydrateBranch(
        SpyBranch $branchEntity,
        BranchTransfer $branchTransfer
    ): void
    {
        foreach ($this->hydratorPlugins as $hydratorPlugin) {
            $hydratorPlugin->hydrateBranch($branchEntity, $branchTransfer);
        }
    }

    /**
     * @param string $status
     * @throws BranchNotFoundException
     * @throws PropelException
     */
    protected function changeStatusForCurrentBranch(string $status)
    {
        $branchTransfer = $this
            ->getCurrentBranch();

        $entity = $this
            ->queryContainer
            ->queryBranchById($branchTransfer->getIdBranch())
            ->findOne();

        if ($entity === null) {
            throw new BranchNotFoundException(
                sprintf(
                    BranchNotFoundException::CODE_NOT_FOUND,
                    $branchTransfer->getIdBranch()
                )
            );
        }

        $paymentMethodIds = [
            self::PAYMENT_METHOD_IDS_KEY_B2B => [],
            self::PAYMENT_METHOD_IDS_KEY_B2C => []
        ];

        foreach ($entity->getSpyBranchToPaymentMethods() as $paymentMethod) {
            if ($paymentMethod->getB2b() === true) {
                $paymentMethodIds[self::PAYMENT_METHOD_IDS_KEY_B2B][] = $paymentMethod->getFkPaymentMethod();
            }
            if ($paymentMethod->getB2c() === true) {
                $paymentMethodIds[self::PAYMENT_METHOD_IDS_KEY_B2C][] = $paymentMethod->getFkPaymentMethod();
            }
        }

        $entity->setStatus($status);
        $entity->save();

        $this->runSaverPlugins($entity, $branchTransfer);
        $this->runStatusIndependentSaverPlugins($entity, $branchTransfer);

        $this
            ->setCurrentBranch($this->entityToTransfer($entity, $paymentMethodIds));
    }

    /**
     * @param SpyBranch $branchEntity
     * @param BranchTransfer $branchTransfer
     */
    protected function runRemovePlugins(
        SpyBranch $branchEntity,
        BranchTransfer $branchTransfer
    )
    {
        foreach ($this->removePlugins as $removePlugin) {
            $removePlugin->removeBranch($branchEntity, $branchTransfer);
        }
    }

    /**
     * @param int $idSalutation
     * @return bool
     * @throws AmbiguousComparisonException
     */
    protected function hasSalutationById(int $idSalutation): bool
    {
        return $this
                ->queryContainer
                ->queryEnumSalutation()
                ->filterByIdEnumSalutation($idSalutation)
                ->count() > 0;
    }

    /**
     * @param int $idPaymentMethod
     * @return bool
     * @throws AmbiguousComparisonException
     */
    protected function hasPaymentMethodById(int $idPaymentMethod): bool
    {
        return $this
                ->queryContainer
                ->queryPaymentMethod()
                ->filterByIdPaymentMethod($idPaymentMethod)
                ->count() > 0;
    }

    /**
     * @param int $idBranch
     * @param int $idPaymentMethod
     * @return bool
     * @throws AmbiguousComparisonException
     */
    protected function hasBranchToPaymentMethodByIdBranchIdPaymentMethod(int $idBranch, int $idPaymentMethod): bool
    {
        return $this
                ->queryContainer
                ->queryBranchToPaymentMethod()
                ->filterByFkBranch($idBranch)
                ->filterByFkPaymentMethod($idPaymentMethod)
                ->count() > 0;
    }

    /**
     * @return string
     */
    protected function createBranchKey(): string
    {
        return sprintf('%s:currentBranch', static::BRANCH_SESSION_KEY);
    }

    /**
     * @return BranchTransfer|null
     */
    protected function readNewBranchFromSession(): ?BranchTransfer
    {
        $key = $this->createBranchKey();

        if (!$this->session->has($key)) {
            return null;
        }

        return $this->session->get($key);
    }

    /**
     * @param SpyBranch $branchEntity
     * @param BranchTransfer $branchTransfer
     */
    protected function runBranchPreSavePlugins(
        SpyBranch $branchEntity,
        BranchTransfer $branchTransfer
    )
    {
        foreach ($this->preSavePlugins as $preSavePlugin) {
            $preSavePlugin->saveBranch($branchEntity, $branchTransfer);
        }
    }

    /**
     * @param SpyBranch $branchEntity
     * @param BranchTransfer $branchTransfer
     */
    protected function runPluginsBasedOnBranchStatus(SpyBranch $branchEntity, BranchTransfer $branchTransfer)
    {
        if (in_array($branchTransfer->getStatus(), [SpyBranchTableMap::COL_STATUS_BLOCKED, SpyBranchTableMap::COL_STATUS_DELETED])) {
            $this->runRemovePlugins($branchEntity, $branchTransfer);
        } else {
            $this->runSaverPlugins($branchEntity, $branchTransfer);
        }
    }

    /**
     * @param SpyBranch $branchEntity
     * @param BranchTransfer $branchTransfer
     */
    protected function runSaverPlugins(
        SpyBranch $branchEntity,
        BranchTransfer $branchTransfer
    )
    {
        foreach ($this->saverPlugins as $saverPlugin) {
            $saverPlugin->saveBranch($branchEntity, $branchTransfer);
        }
    }

    /**
     * @param SpyBranch $branchEntity
     * @param BranchTransfer $branchTransfer
     */
    protected function runStatusIndependentSaverPlugins(
        SpyBranch $branchEntity,
        BranchTransfer $branchTransfer
    )
    {
        foreach ($this->statusIndependentSaverPlugins as $saverPlugin) {
            $saverPlugin->saveBranch($branchEntity, $branchTransfer);
        }
    }

    /**
     * @param int $id
     * @return SpyBranch
     * @throws BranchNotFoundException
     */
    protected function getEntityBranchById(int $id): SpyBranch
    {
        $entity = $this
            ->queryContainer
            ->queryBranchById($id)
            ->findOne();

        if ($entity === null) {
            throw new BranchNotFoundException();
        }

        return $entity;
    }

    /**
     * @param string|null $plainKey
     * @return string|null
     */
    protected function encryptHeidelpayPrivateKey(?string $plainKey): ?string
    {
        if ($plainKey === null) {
            return null;
        }

        $key = hash(
            'sha256',
            $this->config->getHeidelpayPrivateKeyKey()
        );

        $vi = substr(
            hash(
                'sha256',
                $this->config->getHeidelpayPrivateKeyVI()
            ),
            0,
            16
        );

        $output = openssl_encrypt(
            $plainKey,
            $this->config->getHeidelpayPrivateKeyMethod(),
            $key,
            0,
            $vi
        );

        return base64_encode($output);
    }

    /**
     * @param string|null $encryptedKey
     * @return string|null
     */
    protected function decryptHeidelpayPrivateKey(?string $encryptedKey): ?string
    {
        if ($encryptedKey === null) {
            return null;
        }

        $key = hash(
            'sha256',
            $this->config->getHeidelpayPrivateKeyKey()
        );

        $vi = substr(
            hash(
                'sha256',
                $this->config->getHeidelpayPrivateKeyVI()
            ),
            0,
            16
        );

        $unencryptedCode = openssl_decrypt(
            base64_decode($encryptedKey),
            $this->config->getHeidelpayPrivateKeyMethod(),
            $key,
            0,
            $vi
        );

        return $unencryptedCode;
    }
}
