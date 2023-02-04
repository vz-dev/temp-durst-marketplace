<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 19.11.18
 * Time: 10:10
 */

namespace Pyz\Zed\Merchant\Business;


use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\BranchUserTransfer;
use Generated\Shared\Transfer\CartChangeTransfer;
use Generated\Shared\Transfer\CartPreCheckResponseTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\DepositSkuTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\MerchantUserTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\PageMapTransfer;
use Generated\Shared\Transfer\PaymentMethodTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SalutationTransfer;
use Pyz\Zed\Merchant\Business\Exception\MerchantNotFoundException;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Search\Business\Model\Elasticsearch\DataMapper\PageMapBuilderInterface;

interface MerchantFacadeInterface
{

    /**
     * Build a page map for transferring Propel entity Branch to JSON for Elasticsearch
     *
     * @param PageMapBuilderInterface $pageMapBuilder
     * @param array $branchData
     * @param LocaleTransfer $localeTransfer
     * @return PageMapTransfer
     */
    public function buildBranchPageMap(PageMapBuilderInterface $pageMapBuilder, array $branchData, LocaleTransfer $localeTransfer) : PageMapTransfer;

    /**
     * Build a page map for transferring Propel entity PaymentMethod to JSON for Elasticsearch
     *
     * @param PageMapBuilderInterface $pageMapBuilder
     * @param array $paymentProviderData
     * @param LocaleTransfer $localeTransfer
     * @return PageMapTransfer
     */
    public function buildPaymentProviderPageMap(PageMapBuilderInterface $pageMapBuilder, array $paymentProviderData, LocaleTransfer $localeTransfer) : PageMapTransfer;

    /**
     * Specification:
     *  - Checks whether a branch and a payment is selected in the quote
     *  - Checks if the branch supports the selected payment method
     *  - true is returned if so
     *  - otherwise an error transfer is added to the response transfer and false is returned
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return bool
     */
    public function checkBranchSupportsPaymentMethod(
        QuoteTransfer $quoteTransfer,
        CheckoutResponseTransfer $checkoutResponseTransfer
    ): bool;

    /**
     * Specification:
     *  - Adds information about the branch to which the order belongs
     *  - The branch cannot be null as every order has a branch
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    public function hydrateOrderByBranch(OrderTransfer $orderTransfer): OrderTransfer;

    /**
     * Specification:
     *  - Returns the branch with the given branch code
     *  - If no branch with the given code can be found an Exception will be thrown
     *
     * @param string $branchCode
     * @return \Generated\Shared\Transfer\BranchTransfer
     */
    public function getBranchByBranchCode(string $branchCode): BranchTransfer;

    /**
     * Checks if there is a branch set in the session.
     *
     * @return bool returns true if a branch has been chosen and is stored in the session
     */
    public function hasCurrentBranch(): bool;

    /**
     * Returns the merchant who is currently logged in. Throws an exception if
     * no merchant is logged in, so this needs to be check beforehand @see $this->hasCurrentMerchant()
     *
     * @return \Generated\Shared\Transfer\MerchantTransfer
     */
    public function getCurrentMerchant(): MerchantTransfer;

    /**
     * Returns the branch which is currently selected. Throws an exception if
     * no branch is selected, so this needs to be check beforehand @see $this->hasCurrentBranch()
     *
     * @return \Generated\Shared\Transfer\BranchTransfer
     */
    public function getCurrentBranch(): BranchTransfer;

    /**
     * Stores the given branch as currently selected branch in the session.
     *
     * @param BranchTransfer $branch
     * @return mixed
     */
    public function setCurrentBranch(BranchTransfer $branch);

    /**
     * Removes the branch choice from the session
     *
     * @return mixed
     */
    public function unsetCurrentBranch();

    /**
     * Stores the given merchant in the database. A transfer object hydrated
     * with additional data e.g. idMerchant will be returned.
     *
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     * @return MerchantTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws Exception\MerchantExistsException    if a merchant with the given merchantname
     *                                              already exists in the database
     */
    public function createMerchant(MerchantTransfer $merchantTransfer) : MerchantTransfer;

    /**
     * Finds all branches that are related to a merchant defined by its id
     *
     * @param int $idMerchant
     * @return BranchTransfer[]
     */
    public function getBranchesByIdMerchant(int $idMerchant): array;

    /**
     * Stores the given branch in the database
     *
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     */
    public function createBranch(BranchTransfer $branchTransfer);

    /**
     * Updates the data of a merchant in the database, so it matches the given transfer
     * object. The updated transfer object will be returned.
     *
     * @param MerchantTransfer $merchantTransfer
     * @return MerchantTransfer returns the updated transfer object
     */
    public function updateMerchant(MerchantTransfer $merchantTransfer): MerchantTransfer;

    /**
     * Provides a soft delete for a merchant defined by its id.
     * The merchant will not be removed from the database but its status will be set
     * to deleted.
     *
     * @param int $idMerchant
     * @return MerchantTransfer
     */
    public function removeMerchant(int $idMerchant): MerchantTransfer;


    /**
     * Activates the merchant matching the given id.
     *
     * @param int $idMerchant
     * @return bool returns true if the activation has been successful
     */
    public function activateMerchant(int $idMerchant): bool;

    /**
     * Deactivates the merchant matching the given id.
     *
     * @param int $idMerchant
     * @return bool returns true if the deactivation has been successful
     */
    public function deactivateMerchant(int $idMerchant): bool;

    /**
     * Checks whether there is a merchant with a given merchantname.
     *
     * @param string $merchantname
     * @return bool return true if there is a merchant with the given name in the database
     */
    public function hasMerchantByMerchantname(string $merchantname): bool;

    /**
     * Returns a fully hydrated transfer object representing the branch
     * matching the given id. If not branch with the id can be found
     * an exception is will be thrown
     *
     * @param int $idBranch
     * @return BranchTransfer
     */
    public function getBranchById(int $idBranch): BranchTransfer;

    /**
     * Adds a given number of ordered units to the count of ordered units of the branch
     * given by it's id.
     *
     * @param int $idBranch
     * @param int $orderedUnits
     * @return void
     */
    public function sumUpOrderedUnitsToBranchById(int $idBranch, int $orderedUnits): void;

    /**
     * Checks whether there is a merchant matching the given name where the
     * status is "active"
     *
     * @param string $merchantname
     * @return bool
     */
    public function hasActiveMerchantByMerchantname(string $merchantname): bool;

    /**
     * Returns a fully hydrated transfer object representing the merchant defined
     * by the given id. If no merchant can be found with the given name an exception will be thrown.
     *
     * @param int $idMerchant
     * @param bool $hasBranchUser
     * @return MerchantTransfer
     */
    public function getActiveMerchantById(int $idMerchant, bool $hasBranchUser = false): MerchantTransfer;

    /**
     * Returns a fully hydrated transfer object representing the merchant defined
     * by the given name. As the name is unique, the merchant can be identified through it.
     * If no merchant can be found with the given name an exception will be thrown, so
     * make sure to check first via @see hasActiveMerchantByMerchantname.
     *
     * @param string $merchantname
     * @return MerchantTransfer
     */
    public function getMerchantByMerchantname(string $merchantname): MerchantTransfer;

    /**
     * Checks weather the given password matches the hash.
     *
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function isValidPassword(string $password, string $hash): bool;

    /**
     * Stores the given merchant as currently logged in merchant in the session.
     *
     * @param MerchantTransfer $merchant
     * @return mixed
     */
    public function setCurrentMerchant(MerchantTransfer $merchant);

    /**
     * Returns a fully hydrated transfer object representing the merchant defined
     * by the given id. If no merchant with the given id exists an exception will
     * be thrown.
     *
     * @param int $idMerchant
     * @return MerchantTransfer
     */
    public function getMerchantById(int $idMerchant): MerchantTransfer;

    /**
     * Checks whether there is a merchant currently logged in.
     *
     * @return bool
     */
    public function hasCurrentMerchant(): bool;

    /**
     * Returns an array of fully hydrated transfer objects, representing all merchants
     * that are stored in the database.
     *
     * @return MerchantTransfer[]
     */
    public function getMerchants(): array;

    /**
     * Checks whether the given route can be accessed without previously selecting
     * a branch.
     *
     * @param string $bundle
     * @param string $controller
     * @param string $action
     * @return bool
     */
    public function isBranchIgnorable(string $bundle, string $controller, string $action): bool;

    /**
     * Updates the data of a merchant in the database, so it matches the given transfer
     * object. The updated transfer object will be returned.
     *
     * @param BranchTransfer $branchTransfer
     * @return BranchTransfer returns the updated branch transfer
     */
    public function updateBranch(BranchTransfer $branchTransfer): BranchTransfer;

    /**
     * Adds a payment method represented by its id to the given branch.
     * The updated, fully hydrated tranfer object will be returned.
     *
     * @param int $idPaymentMethod
     * @param BranchTransfer $branchTransfer
     * @return BranchTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function addPaymentMethodToBranch(int $idPaymentMethod, BranchTransfer $branchTransfer): BranchTransfer;

    /**
     * Adds a new payment method to the database. The data is provided by the given transfer
     * object. The fully hydrated transfer object will be returned
     *
     * @param PaymentMethodTransfer $paymentMethodTransfer
     * @return PaymentMethodTransfer
     * @throws Exception\PaymentMethodExistsException if the idPaymentMethod is set
     * in the transfer object
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function addPaymentMethod(PaymentMethodTransfer $paymentMethodTransfer): PaymentMethodTransfer;

    /**
     * Updates the payment method represented by a transfer object in the database and
     * returns a new fully hydrated transfer object containing the updated data.
     *
     * @param PaymentMethodTransfer $paymentMethodTransfer
     * @return PaymentMethodTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\PaymentMethodNotFoundException if no payment method with the
     * id matching the transfer object can be found in the database.
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function updatePaymentMethod(PaymentMethodTransfer $paymentMethodTransfer): PaymentMethodTransfer;

    /**
     * Returns a fully hydrated transfer object representing a payment method
     * defined by its id.
     *
     * @param int $idPaymentMethod
     * @return PaymentMethodTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\PaymentMethodNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getPaymentMethodById(int $idPaymentMethod): PaymentMethodTransfer;

    /**
     * Removes the payment method with the given id from the database.
     *
     * @param int $idPaymentMethod
     * @return void
     * @throws \Pyz\Zed\Merchant\Business\Exception\PaymentMethodNotFoundException if no payment method with the given id
     * could be found in the database
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function removePaymentMethod(int $idPaymentMethod): void;

    /**
     * Returns the id of a payment method with the given code.
     *
     * @param string $code
     * @return int
     * @throws \Pyz\Zed\Merchant\Business\Exception\PaymentMethodNotFoundException if no payment method with the
     * given id can be found
     */
    public function getPaymentMethodIdByCode(string $code): int;

    /**
     * Adds a new salutation defined by the given transfer object to the database.
     *
     * @param \Generated\Shared\Transfer\SalutationTransfer $salutationTransfer
     * @return SalutationTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function addSalutation(SalutationTransfer $salutationTransfer): SalutationTransfer;

    /**
     * Updates a salutation in the database so it matches the given transfer object.
     *
     * @param SalutationTransfer $salutationTransfer
     * @return SalutationTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\SalutationIdNotSetException if the given transfer object has no id
     * set (can't find salutation without it's id)
     * @throws \Pyz\Zed\Merchant\Business\Exception\SalutationNotFoundException if no salutation with the id set in the transfer
     * object could be found in the database
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function updateSalutation(SalutationTransfer $salutationTransfer): SalutationTransfer;

    /**
     * Receives a salutation from the database matching the given id and returnes
     * a fully hydrated transfer object.
     *
     * @param int $idSalutation
     * @return SalutationTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\SalutationNotFoundException if no salutation with the given id
     * could be found in the database
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getSalutationById(int $idSalutation): SalutationTransfer;

    /**
     * @param int $idSalutation
     * @return void
     * @throws \Pyz\Zed\Merchant\Business\Exception\SalutationNotFoundException if no salutation with the given id
     * could be found in the database
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function removeSalutation(int $idSalutation): void;

    /**
     * Removes the payment method matching the id from the branch with the
     * given id.
     *
     * @param int $idPaymentMethod
     * @param int $idBranch
     * @return void
     */
    public function removePaymentMethodFromBranch(int $idPaymentMethod, int $idBranch): void;

    /**
     * Returns hydrated transfer objects for all branches in the database.
     *
     * @return BranchTransfer[]
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getBranches(): array;

    /**
     * Returns an array of transfer objects representing all branches that have at least
     * one active time slot for a delivery area specified by the given zip code.
     *
     * @param string $zipCode
     * @return BranchTransfer[]
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\DeliveryArea\Business\Exception\DeliveryAreaNotFoundException if no delivery area
     * in the database matches the given zip code
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getBranchesByZipCode(string $zipCode): array;

    /**
     * Persists the given branch transfer to the database and returns a fully hydrated transfer object.
     *
     * @param BranchTransfer $branchTransfer
     * @return BranchTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\BranchNotFoundException
     * @throws \Pyz\Zed\Merchant\Business\Exception\Code\CodeExistsException
     * @throws \Pyz\Zed\Merchant\Business\Exception\Code\CodeMalformedException
     * @throws \Pyz\Zed\Merchant\Business\Exception\SalutationNotFoundException
     * @throws \Pyz\Zed\Merchant\Business\Exception\PaymentMethodNotFoundException
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function addBranch(BranchTransfer $branchTransfer) : BranchTransfer;

    /**
     * Removes the branch with the given id from the database. This is a soft delete, as the data set
     * won't be deleted but the status will be set to "deleted".
     *
     * @param int $idBranch
     * @return void
     */
    public function removeBranch(int $idBranch): void;

    /**
     * This will return a fully hydrated branch transfer matching the given code.
     *
     * @param string $code
     * @return BranchTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\BranchNotFoundException if on branch with the given code can be found in
     * the database
     */
    public function getBranchByCode(string $code): BranchTransfer;

    /**
     * Checks whether the branch with the given id supports the given payment method.
     *
     * @param int $idBranch
     * @param string $paymentMethod
     * @return bool
     */
    public function hasBranchPaymentMethod(int $idBranch, string $paymentMethod) : bool;

    /**
     * Returns an array of all payment methods that are supported by the branches with
     * the given ids.
     *
     * @param array $branchIds
     * @return PaymentMethodTransfer[]
     */
    public function getSupportedPaymentMethodsForBranches(array $branchIds) : array;

    /**
     * Returns an array of all payment methods.
     *
     * @return PaymentMethodTransfer[]
     */
    public function getPaymentMethods() : array;

    /**
     * Sets the status of the branch that is currently logged in to active.
     *
     * @return void
     */
    public function activateCurrentBranch(): void;

    /**
     * Sets the status of the branch that is currently logged in to blocked.
     *
     * @return void
     */
    public function deactivateCurrentBranch(): void;

    /**
     * Checks whether the branch for which products should be added to the cart exists and is active.
     *
     * @param \Generated\Shared\Transfer\CartChangeTransfer $cartChangeTransfer
     * @return \Generated\Shared\Transfer\CartPreCheckResponseTransfer
     */
    public function validateBranch(CartChangeTransfer $cartChangeTransfer) : CartPreCheckResponseTransfer;

    /**
     * Changes the status of the given branch from deleted to blocked.
     *
     * @param int $idBranch
     * @return void
     */
    public function restoreBranch(int $idBranch): void;

    /**
     * Checks whether the branch for which a checkout should be placed exists and is active.
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     * @return bool
     */
    public function validateBranchForCheckout(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer): bool;

    /**
     * Returns a fully hydrated transfer object representing the branch with the given id. If the branch
     * does not deliver to the delivery area with the given zip code or no branch with the given id
     * exists null will be returned.
     *
     * @param int $id
     * @param string $zipCode
     * @return BranchTransfer|null
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getBranchByIdAndZipCode(int $id, string $zipCode): ?BranchTransfer;

    /**
     * Returns all payment methods that can be selected by the current branch.
     * This is defined by the software package the merchant has purchased.
     *
     * @return PaymentMethodTransfer[]
     */
    public function getPossiblePaymentMethodsForCurrentBranch(): array;

    /**
     * Returns all payment methods that can be selected by the branch with the given ID.
     * This is defined by the software package the merchant has purchased.
     *
     * @param int $idBranch
     *
     * @return PaymentMethodTransfer[]
     */
    public function getPossiblePaymentMethodsByIdBranch(int $idBranch): array;

    /**
     * Returns all payment methods that can be selected by the branch based on the branches merchant.
     * This is defined by the software package the merchant has purchased.
     *
     * @param int $idMerchant
     *
     * @return PaymentMethodTransfer[]
     */
    public function getPossiblePaymentMethodsForBranchByMerchantId(int $idMerchant): array;

    /**
     * Returns a transfer object representing the payment method matching the given code.
     *
     * @param string $code
     * @return PaymentMethodTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\PaymentMethodNotFoundException   if no payment method with the given code
     *                                          can be found.
     */
    public function getPaymentMethodByCode(string $code) : PaymentMethodTransfer;

    /**
     * Returns a BranchTransfer for the provided hash the matching branch is determined by
     * using the getHashForBranch function
     *
     * @param string $hash
     * @return BranchTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\BranchNotFoundException  if no Branch can be found that has a matching
     */
    public function getBranchByHash(string $hash): BranchTransfer;

    /**
     * Returns a md5 hashed string based on the following items for the provided BranchTransfer
     * Merchant ID + Branch Name + Branch ID + Branch Email + Salt
     *
     * @param BranchTransfer $branchTransfer
     * @return string
     */
    public function getHashForBranch(BranchTransfer $branchTransfer): string;

    /**
     * Returns all deposit skus for a given branch in form of DepositSkuTransfers
     *
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     * @return \Generated\Shared\Transfer\DepositSkuTransfer[]
     */
    public function getDepositSkusForBranch(BranchTransfer $branchTransfer): array;

    /**
     * Persists the deposit skus to the database.
     *
     * @param iterable $depositSkus
     * @return void
     */
    public function updateDepositSkus(iterable $depositSkus): void;

    /**
     * Returns all deposit skus for a given branch that the branch accepts and has entered
     * a sku for in form of DepositSkuTransfers
     *
     * @param BranchTransfer $branchTransfer
     * @return \Generated\Shared\Transfer\DepositSkuTransfer[]
     */
    public function getAcceptedDepositSkusForBranch(BranchTransfer $branchTransfer) : array;

    /**
     * Returns a deposit sku transfer for the current branch which matches the deposit passed
     *
     * @param int $idBranch
     * @param int $idDeposit
     * @return \Generated\Shared\Transfer\DepositSkuTransfer
     */
    public function getDepositSkuByDepositIdForBranch(int $idBranch, int $idDeposit): DepositSkuTransfer;

    /**
     * Adds a given number of license units to the count of license units of the branch
     * given by it's id.
     *
     * @param int $idBranch
     * @param int $licenseUnits
     * @return void
     */
    public function sumUpLicenseUnitsToBranchById(int $idBranch, int $licenseUnits): void;

    /**
     * Checks whether there is a merchant with a given merchantPin.
     *
     * @param string $merchantPin
     * @return bool return true if there is a merchant with the given pin in the database
     */
    public function hasMerchantByMerchantPin(string $merchantPin): bool;

    /**
     * Checks whether there is a merchant matching the given pin where the
     * status is "active"
     *
     * @param string $merchantPin
     * @return bool
     */
    public function hasActiveMerchantByMerchantPin(string $merchantPin): bool;

    /**
     * Returns a fully hydrated transfer object representing the merchant defined
     * by the given pin. As the pin is unique, the merchant can be identified through it.
     * If no merchant can be found with the given pin an exception will be thrown, so
     * make sure to check first via @see hasActiveMerchantByMerchantPin.
     *
     * @param string $merchantPin
     * @return MerchantTransfer
     */
    public function getMerchantByMerchantPin(string $merchantPin): MerchantTransfer;

    /**
     * Return a fully hydrated transfer object representing the branch user
     * defined by the given id.
     *
     * @param int $idBranchUser
     * @return \Generated\Shared\Transfer\BranchUserTransfer
     */
    public function getBranchUserById(int $idBranchUser): BranchUserTransfer;

    /**
     * Return a fully hydrated transfer object representing the branch user
     * defined by the given email
     *
     * @param string $email
     * @return \Generated\Shared\Transfer\BranchUserTransfer
     */
    public function getBranchUserByEmail(string $email): BranchUserTransfer;

    /**
     * Store a branch user in the database.
     * Return a fully hydrated transfer object, e.g. with idBranchUser set.
     *
     * @param \Generated\Shared\Transfer\BranchUserTransfer $branchUserTransfer
     * @return \Generated\Shared\Transfer\BranchUserTransfer
     */
    public function createBranchUser(BranchUserTransfer $branchUserTransfer): BranchUserTransfer;

    /**
     * Updates the data of a branch user in the database, so it matches the given transfer
     * object. The updated transfer object will be returned.
     *
     * @param \Generated\Shared\Transfer\BranchUserTransfer $branchUserTransfer
     * @return \Generated\Shared\Transfer\BranchUserTransfer
     */
    public function updateBranchUser(BranchUserTransfer $branchUserTransfer): BranchUserTransfer;

    /**
     * Set status of given branch user to active
     *
     * @param int $idBranchUser
     * @return bool
     */
    public function activateBranchUser(int $idBranchUser): bool;

    /**
     * Set status of given branch user to deactivated
     *
     * @param int $idBranchUser
     * @return bool
     */
    public function deactivateBranchUser(int $idBranchUser): bool;

    /**
     * Mark gievn branch user as deleted
     *
     * @param int $idBranchUser
     * @return bool
     */
    public function deleteBranchUser(int $idBranchUser): bool;

    /**
     * Check, if an active branch user with the given email exists
     *
     * @param string $email
     * @return bool
     */
    public function hasActiveBranchUserByEmail(string $email): bool;

    /**
     * Verify, if a given password matches the given hash
     *
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function isValidBranchUserPassword(string $password, string $hash): bool;

    /**
     * Get a hydrated merchant for a given branch id
     *
     * @param int $idBranch
     * @return \Generated\Shared\Transfer\MerchantTransfer
     */
    public function getMerchantByIdBranch(int $idBranch): MerchantTransfer;

    /**
     * Checks if there is a branch user set in the session.
     *
     * @return bool
     */
    public function hasCurrentBranchUser(): bool;

    /**
     * Stores the given branch user as currently logged in branch user in the session.
     *
     * @param \Generated\Shared\Transfer\BranchUserTransfer $branchUserTransfer
     * @return mixed
     */
    public function setCurrentBranchUser(BranchUserTransfer $branchUserTransfer);

    /**
     * Returns the branch user who is currently logged in. Throws an exception if
     * no branch user is logged in, so this needs to be check beforehand @see $this->hasCurrentBranchUser()
     *
     * @return \Generated\Shared\Transfer\BranchUserTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\BranchUserNotFoundException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getCurrentBranchUser(): BranchUserTransfer;

    /**
     * Removes the branch user from the session
     *
     * @return void
     */
    public function unsetCurrentBranchUser(): void;

    /**
     * Removes the merchant from the session
     *
     * @return void
     */
    public function unsetCurrentMerchant(): void;

    /**
     * Get a list of all branch users for a branch
     * Identified by the given id of the branch
     *
     * @param int $idBranch
     * @return BranchUserTransfer[]
     */
    public function getBranchUsersByIdBranch(int $idBranch): array;

    /**
     * Return a fully hydrated transfer object representing the merchant user
     * defined by the given id.
     *
     * @param int $idMerchantUser
     * @return \Generated\Shared\Transfer\MerchantUserTransfer
     */
    public function getMerchantUserById(int $idMerchantUser): MerchantUserTransfer;

    /**
     * Return a fully hydrated transfer object representing the merchant user
     * defined by the given email
     *
     * @param string $email
     * @return \Generated\Shared\Transfer\MerchantUserTransfer
     */
    public function getMerchantUserByEmail(string $email): MerchantUserTransfer;

    /**
     * Store a merchant user in the database.
     * Return a fully hydrated transfer object, e.g. with idMerchantUser set.
     *
     * @param \Generated\Shared\Transfer\MerchantUserTransfer $merchantUserTransfer
     * @return \Generated\Shared\Transfer\MerchantUserTransfer
     */
    public function createMerchantUser(MerchantUserTransfer $merchantUserTransfer): MerchantUserTransfer;

    /**
     * Updates the data of a merchant user in the database, so it matches the given transfer
     * object. The updated transfer object will be returned.
     *
     * @param \Generated\Shared\Transfer\MerchantUserTransfer $branchUserTransfer
     * @return \Generated\Shared\Transfer\MerchantUserTransfer
     */
    public function updateMerchantUser(MerchantUserTransfer $branchUserTransfer): MerchantUserTransfer;

    /**
     * Set status of given merchant user to active
     *
     * @param int $idMerchantUser
     * @return bool
     */
    public function activateMerchantUser(int $idMerchantUser): bool;

    /**
     * Set status of given merchant user to deactivated
     *
     * @param int $idMerchantUser
     * @return bool
     */
    public function deactivateMerchantUser(int $idMerchantUser): bool;

    /**
     * Mark given merchant user as deleted
     *
     * @param int $idMerchantUser
     * @return bool
     */
    public function deleteMerchantUser(int $idMerchantUser): bool;

    /**
     * Check, if an active merchant user with the given email exists
     *
     * @param string $email
     * @return bool
     */
    public function hasActiveMerchantUserByEmail(string $email): bool;

    /**
     * Verify, if a given password matches the given hash
     *
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function isValidMerchantUserPassword(
        string $password,
        string $hash
    ): bool;

    /**
     * Checks if there is a merchant user set in the session.
     *
     * @return bool
     */
    public function hasCurrentMerchantUser(): bool;

    /**
     * Stores the given merchant user as currently logged in merchant user in the session.
     *
     * @param \Generated\Shared\Transfer\MerchantUserTransfer $merchantUserTransfer
     * @return mixed
     */
    public function setCurrentMerchantUser(MerchantUserTransfer $merchantUserTransfer);

    /**
     * Returns the merchant user who is currently logged in. Throws an exception if
     * no merchant user is logged in, so this needs to be checked beforehand @see $this->hasCurrentMerchantUser()
     *
     * @return \Generated\Shared\Transfer\MerchantUserTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Pyz\Zed\Merchant\Business\Exception\MerchantUserNotFoundException
     */
    public function getCurrentMerchantUser(): MerchantUserTransfer;

    /**
     * Removes the merchant user from the session
     *
     * @return void
     */
    public function unsetCurrentMerchantUser(): void;

    /**
     * Get a list of all merchant users for a merchant
     * Identified by the given id of the merchant
     *
     * @param int $idMerchant
     * @return array|MerchantUserTransfer[]
     */
    public function getMerchantUsersByIdMerchant(int $idMerchant): array;

    /**
     * Returns the e-mail address of the user who is currently logged in
     *
     * @return string
     * @throws ContainerKeyNotFoundException
     * @throws MerchantNotFoundException
     */
    public function getCurrentUserEmail(): string;
}
