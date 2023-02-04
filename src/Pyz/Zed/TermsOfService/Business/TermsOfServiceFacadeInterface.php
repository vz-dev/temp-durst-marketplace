<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 12.01.18
 * Time: 13:56
 */

namespace Pyz\Zed\TermsOfService\Business;


use Generated\Shared\Transfer\TermsOfServiceTransfer;

interface TermsOfServiceFacadeInterface
{
    /**
     * Checks whether there are any terms of service objects in the database
     *
     * @return bool
     */
    public function termsOfServiceAreImported();

    /**
     * Adds a new terms of service object defined by the given transfer object to
     * the database. A fully hydrated transfer object representing the added
     * data set will be returned.
     *
     * @param TermsOfServiceTransfer $transfer
     * @return TermsOfServiceTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function add(TermsOfServiceTransfer $transfer);

    /**
     * Updates the data of a terms of service object defined by the given
     * transfer object in the database. The updated version of the transfer object
     * will be returned.
     *
     * @param TermsOfServiceTransfer $transfer
     * @return TermsOfServiceTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function update(TermsOfServiceTransfer $transfer);

    /**
     * Removes the terms of service object defined by the given id from the database.
     * (This is a soft delete for archive purposes)
     *
     * @param int $idTermsOfService
     * @return void
     * @throws Exception\TermsOfServiceNotFoundException if no terms of service object
     * with the given id can be found in the database
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function remove($idTermsOfService);

    /**
     * Returns a fully hydrated transfer object containing all data of the terms
     * of service object defined by the given id.
     *
     * @param int $idTermsOfService
     * @return TermsOfServiceTransfer
     * @throws Exception\TermsOfServiceNotFoundException if no terms of service object
     * with the given id can be found in the database
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getTermsOfServiceById($idTermsOfService);

    /**
     * Returns a fully hydrated transfer object representing one terms of service object
     * a merchant (defined by the given id) needs to accept, before proceeding.
     *
     * @param int $idMerchant
     * @return TermsOfServiceTransfer
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getUnacceptedTermsOfServiceByIdMerchant($idMerchant);

    /**
     * Checks whether there are any terms of service objects that need to be accepted
     * for the given merchant defined by its id.
     *
     * @param int $idMerchant
     * @return bool
     */
    public function hasUnacceptedTermsOfServiceByIdMerchant($idMerchant);

    /**
     * Checks whether the given route can be accessed without previously accepting
     * all active terms of service.
     *
     * @param string $bundle
     * @param string $controller
     * @param string $action
     * @return bool
     */
    public function isRouteIgnorable($bundle, $controller, $action);

    /**
     * Accepts the terms of service object defined by its id for the merchant defined
     * by the given id.
     *
     * @param int $idTermsOfService
     * @param int $idMerchant
     * @return void
     * @throws Exception\TermsOfServiceAlreadyAcceptedException if the given terms of service
     * object has already been accepted by the merchant with the given id
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function acceptTermsOfServiceByIdForMerchantById($idTermsOfService, $idMerchant);

    /**
     * Receives the terms of service data set with the name defined in the config and returns a fully hydrated
     * transfer object.
     *
     * @return TermsOfServiceTransfer
     */
    public function getCustomerTerms() : TermsOfServiceTransfer;

    /**
     * Retrieves the terms of service data set with the name defined in the config and returns a fully hydrated
     * transfer object. The item that is returned is the element that was valid at the given date.
     *
     * @param int $timestamp
     * @return TermsOfServiceTransfer
     */
    public function getActiveCustomerTermsByTimestamp(int $timestamp): TermsOfServiceTransfer;
}
