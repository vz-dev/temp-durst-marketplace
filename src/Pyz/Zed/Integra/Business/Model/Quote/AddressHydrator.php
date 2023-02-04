<?php
/**
 * Durst - project - AddressHydrator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 19.11.20
 * Time: 14:53
 */

namespace Pyz\Zed\Integra\Business\Model\Quote;


use Generated\Shared\Transfer\AddressTransfer;

class AddressHydrator implements AddressHydratorInterface
{
    /**
     * @param array $order
     *
     * @return AddressTransfer
     */
    public function createAddressTransfer(array &$order): AddressTransfer
    {
        return (new AddressTransfer())
            ->setZipCode($order['zip'])
            ->setCity($order['city'])
            ->setEmail($order['email'])
            ->setAddress1($order['address1'])
            ->setFirstName($order['first_name'])
            ->setLastName($order['last_name'])
            ->setPhone($order['phone'])
            ->setSalutation($this->getSalutationFromString($order['salutation']))
            ->setIso2Code('DE');
    }

    /**
     * @param string|null $salutation
     * @return string
     */
    protected function getSalutationFromString(?string $salutation) : string
    {
        if(strtolower($salutation) === 'frau')
        {
            return 'Mrs';
        }

        return 'Mr';
    }
}
