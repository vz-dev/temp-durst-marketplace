<?php
/**
 * Durst - project - ManufacturerRepository.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 11.06.18
 * Time: 16:52
 */

namespace Pyz\Zed\AkeneoPimMiddlewareConnector\Business\Repository;


use Orm\Zed\Product\Persistence\SpyManufacturerQuery;
use Pyz\Zed\AkeneoPimMiddlewareConnector\Exception\ManufacturerEntityNotFoundException;

class ManufacturerRepository
{
    const KEY_CODE = 'code';

    /**
     * @var array
     */
    protected static $resolved = [];

    /**
     * @param string $code
     * @return int
     * @throws ManufacturerEntityNotFoundException
     */
    public function getManufacturerIdByCode(string $code) : int
    {
        if(!isset(static::$resolved[$code])){
            $this->resolveManufacturerByCode($code);
        }

        return static::$resolved[$code];
    }

    /**
     * @param string $code
     * @throws ManufacturerEntityNotFoundException
     */
    protected function resolveManufacturerByCode(string $code) : void
    {
        $manufacturerEntity = SpyManufacturerQuery::create()
            ->findOneByCode($code);

        if($manufacturerEntity === null){
            throw new ManufacturerEntityNotFoundException(
                sprintf(
                    ManufacturerEntityNotFoundException::MESSAGE,
                    $code
                )
            );
        }

        static::$resolved[$code] = $manufacturerEntity->getIdManufacturer();
    }
}