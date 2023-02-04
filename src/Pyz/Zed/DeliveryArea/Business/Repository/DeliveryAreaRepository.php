<?php
/**
 * Durst - project - DeliveryAreaRepository.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-10-23
 * Time: 12:53
 */

namespace Pyz\Zed\DeliveryArea\Business\Repository;


use Orm\Zed\DeliveryArea\Persistence\SpyDeliveryAreaQuery;
use Pyz\Zed\DeliveryArea\Business\Exception\DeliveryAreaNotFoundException;

class DeliveryAreaRepository
{
    const KEY_ZIP_CODE = 'code';
    const KEY_ID_DELIVERY_AREA = 'idDeposit';

    /**
     * @var array
     */
    protected static $resolved = [];

    /**
     * @param string $zip
     * @return int
     * @throws DeliveryAreaNotFoundException
     */
    public function getDeliveryAreaIdByZip(string $zip) : int
    {
        if(!isset(static::$resolved[$zip])){
            $this->resolveDeliveryAreaByZip($zip);
        }

        return static::$resolved[$zip][self::KEY_ID_DELIVERY_AREA];
    }

    /**
     * @param string $zip
     * @throws DeliveryAreaNotFoundException
     */
    protected function resolveDeliveryAreaByZip(string $zip) : void
    {
        $deliveryArea = SpyDeliveryAreaQuery::create()
            ->findOneByZipCode($zip);

        if($deliveryArea === null){
            throw new DeliveryAreaNotFoundException(
                sprintf(
                    DeliveryAreaNotFoundException::NOT_FOUND_ZIP,
                    $zip
                )
            );
        }

        static::$resolved[$zip] = [
            self::KEY_ID_DELIVERY_AREA => $deliveryArea->getIdDeliveryArea()
        ];
    }
}
