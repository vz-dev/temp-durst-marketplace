<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 12.01.18
 * Time: 13:56
 */

namespace Pyz\Zed\TermsOfService\Business;


use Generated\Shared\Transfer\TermsOfServiceTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * Class TermsOfServiceFacade
 * @package Pyz\Zed\TermsOfService\Business
 * @method TermsOfServiceBusinessFactory getFactory()
 */
class TermsOfServiceFacade extends AbstractFacade implements TermsOfServiceFacadeInterface
{
    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function termsOfServiceAreImported()
    {
        return $this
            ->getFactory()
            ->createTermsOfServiceModel()
            ->termsOfServiceAreImported();
    }

    /**
     * {@inheritdoc}
     *
     * @param TermsOfServiceTransfer $transfer
     * @return TermsOfServiceTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function add(TermsOfServiceTransfer $transfer)
    {
        return $this
            ->getFactory()
            ->createTermsOfServiceModel()
            ->save($transfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param TermsOfServiceTransfer $transfer
     * @return TermsOfServiceTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function update(TermsOfServiceTransfer $transfer)
    {
        return $this
            ->getFactory()
            ->createTermsOfServiceModel()
            ->save($transfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idTermsOfService
     * @throws Exception\TermsOfServiceNotFoundException
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function remove($idTermsOfService)
    {
        $this
            ->getFactory()
            ->createTermsOfServiceModel()
            ->remove($idTermsOfService);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idTermsOfService
     * @return TermsOfServiceTransfer
     * @throws Exception\TermsOfServiceNotFoundException
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getTermsOfServiceById($idTermsOfService)
    {
        return $this
            ->getFactory()
            ->createTermsOfServiceModel()
            ->getTermsOfServiceById($idTermsOfService);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idMerchant
     * @return TermsOfServiceTransfer
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function getUnacceptedTermsOfServiceByIdMerchant($idMerchant)
    {
        return $this
            ->getFactory()
            ->createTermsOfServiceModel()
            ->getUnacceptedTermsOfServiceByIdMerchant($idMerchant);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $bundle
     * @param string $controller
     * @param string $action
     * @return bool
     */
    public function isRouteIgnorable($bundle, $controller, $action)
    {
        return $this
            ->getFactory()
            ->createTermsOfServiceModel()
            ->isRouteIgnorable($bundle, $controller, $action);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idMerchant
     * @return bool
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function hasUnacceptedTermsOfServiceByIdMerchant($idMerchant)
    {
        return $this
            ->getFactory()
            ->createTermsOfServiceModel()
            ->hasUnacceptedTermsOfServiceByIdMerchant($idMerchant);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idTermsOfService
     * @param int $idMerchant
     * @return void
     * @throws Exception\TermsOfServiceAlreadyAcceptedException if the given terms of service
     * object has already been accepted by the merchant with the given id
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function acceptTermsOfServiceByIdForMerchantById($idTermsOfService, $idMerchant)
    {
        $this
            ->getFactory()
            ->createTermsOfServiceModel()
            ->acceptTermsOfServiceByIdForMerchantById($idTermsOfService, $idMerchant);
    }

    /**
     * {@inheritdoc}
     *
     * @return TermsOfServiceTransfer
     * @throws Exception\NoCustomerTermsFound
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getCustomerTerms(): TermsOfServiceTransfer
    {
        return $this
            ->getFactory()
            ->createTermsOfServiceModel()
            ->getCustomerTerms();
    }

    /**
     * @param int $timestamp
     * @return TermsOfServiceTransfer
     * @throws Exception\NoCustomerTermsFound
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getActiveCustomerTermsByTimestamp(int $timestamp): TermsOfServiceTransfer
    {
        return $this
            ->getFactory()
            ->createTermsOfServiceModel()
            ->getActiveCustomerTermsByTimestamp($timestamp);
    }
}
