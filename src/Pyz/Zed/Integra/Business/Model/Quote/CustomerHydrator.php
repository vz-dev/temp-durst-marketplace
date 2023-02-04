<?php
/**
 * Durst - project - CustomerHydrator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 20.11.20
 * Time: 10:02
 */

namespace Pyz\Zed\Integra\Business\Model\Quote;


use Generated\Shared\Transfer\CustomerTransfer;

class CustomerHydrator implements CustomerHydratorInterface
{
    /**
     * @param array $orderData
     *
     * @return CustomerTransfer
     */
    public function createCustomerTransfer(array $orderData): CustomerTransfer
    {
        return (new CustomerTransfer())
            ->setFirstName($orderData['first_name'])
            ->setLastName($orderData['last_name'])
            ->setEmail($orderData['email'])
            ->setPhone($orderData['phone'])
            ->setSalutation($this->getSalutationFromString($orderData['salutation']))
            ->setIsPrivate($orderData['b2c'])
            ->setIsGuest(true);
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
