<?php
/**
 * Durst - project - UnitRepository.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 13.04.18
 * Time: 13:54
 */

namespace Pyz\Zed\AkeneoPimMiddlewareConnector\Business\Repository;


use Orm\Zed\Deposit\Persistence\SpyDepositQuery;
use Pyz\Zed\AkeneoPimMiddlewareConnector\Exception\DepositEntityNotFoundException;

class UnitRepository
{
    const KEY_CODE = 'code';
    const KEY_NAME = 'name';
    const KEY_ID_DEPOSIT = 'idDeposit';

    /**
     * @var array
     */
    protected static $resolved = [];

    /**
     * @param string $code
     * @return int
     * @throws DepositEntityNotFoundException
     */
    public function getUnitIdByCode(string $code) : int
    {
        if(!isset(static::$resolved[$code])){
            $this->resolveDepositByCode($code);
        }

        return static::$resolved[$code][self::KEY_ID_DEPOSIT];
    }

    /**
     * @param string $code
     * @return string
     * @throws DepositEntityNotFoundException
     */
    public function getUnitNameByCode(string $code) : string
    {
        if(!isset(static::$resolved[$code])){
            $this->resolveDepositByCode($code);
        }

        return static::$resolved[$code][self::KEY_NAME];
    }

    /**
     * @param string $code
     * @throws DepositEntityNotFoundException
     */
    protected function resolveDepositByCode(string $code) : void
    {
        $depositEntity = SpyDepositQuery::create()
            ->findOneByCode($code);

        if($depositEntity === null){
            throw new DepositEntityNotFoundException(
                sprintf(
                    DepositEntityNotFoundException::MESSAGE,
                    $code
                )
            );
        }

        static::$resolved[$code] = [
            self::KEY_ID_DEPOSIT => $depositEntity->getIdDeposit(),
            self::KEY_NAME => $depositEntity->getName(),
        ];
    }


}