<?php
/**
 * Durst - project - DurstPriceImportMapper.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 05.10.20
 * Time: 11:21
 */

namespace Pyz\Zed\PriceImport\Business\Mapper;


use Pyz\Zed\PriceImport\Business\Manager\PriceImportManager;

class DurstPriceImportMapper implements PriceImportMapperInterface
{
    protected const IMPORT_CSV_DELIMITER = ';';

    /**
     * @var array
     */
    protected $csvHeaderFields = [
        PriceImportManager::IMPORT_KEY_SKU,
        PriceImportManager::IMPORT_KEY_PRODUCT_NAME,
        PriceImportManager::IMPORT_KEY_PRODUCT_UNIT,
        PriceImportManager::IMPORT_KEY_MERCHANT_SKU,
        PriceImportManager::IMPORT_KEY_PRICE_GROSS,
        PriceImportManager::IMPORT_KEY_STATUS
    ];

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function getName(): string
    {
        return static::class;
    }

    /**
     * {@inheritDoc}
     *
     * @return int
     */
    public function getMerchantSkuIndex(): int
    {
        return array_search(
            PriceImportManager::IMPORT_KEY_MERCHANT_SKU,
            $this->getCsvHeaderFields()
        );
    }

    /**
     * {@inheritDoc}
     *
     * @return int
     */
    public function getGrossPriceIndex(): int
    {
        return array_search(
            PriceImportManager::IMPORT_KEY_PRICE_GROSS,
            $this->getCsvHeaderFields()
        );
    }

    /**
     * {@inheritDoc}
     *
     * @return array
     */
    public function getCsvHeaderFields(): array
    {
        return $this->csvHeaderFields;
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function getImportDelimiter(): string
    {
        return static::IMPORT_CSV_DELIMITER;
    }

    /**
     * {@inheritDoc}
     *
     * @param array $csvRow
     * @return array
     */
    public function getMappedRow(array $csvRow): array
    {
        $csvRow = $this
            ->replaceEmptyWithNullAndSetActiveTrueFalse(
                $csvRow
            );

        return [
            PriceImportManager::IMPORT_KEY_SKU_POSITION => $csvRow[$this->getIndexForKey(PriceImportManager::IMPORT_KEY_SKU)],
            PriceImportManager::IMPORT_KEY_PRODUCT_NAME_POSITION => $csvRow[$this->getIndexForKey(PriceImportManager::IMPORT_KEY_PRODUCT_NAME)],
            PriceImportManager::IMPORT_KEY_PRODUCT_UNIT_POSITION => $csvRow[$this->getIndexForKey(PriceImportManager::IMPORT_KEY_PRODUCT_UNIT)],
            PriceImportManager::IMPORT_KEY_MERCHANT_SKU_POSITION => $csvRow[$this->getIndexForKey(PriceImportManager::IMPORT_KEY_MERCHANT_SKU)],
            PriceImportManager::IMPORT_KEY_PRICE_GROSS_POSITION => $csvRow[$this->getIndexForKey(PriceImportManager::IMPORT_KEY_PRICE_GROSS)],
            PriceImportManager::IMPORT_KEY_STATUS_POSITION => $csvRow[$this->getIndexForKey(PriceImportManager::IMPORT_KEY_STATUS)]
        ];
    }

    /**
     * @param array $csvRow
     * @return array
     */
    protected function replaceEmptyWithNullAndSetActiveTrueFalse(array $csvRow) : array
    {
        $activeKey = array_search(
            PriceImportManager::IMPORT_KEY_STATUS,
            $this->getCsvHeaderFields()
        );

        foreach ($csvRow as $key => $value) {
            if ($csvRow[$key] === '') {
                $csvRow[$key] = null;
            }

            if (
                $key == $activeKey &&
                $value === '1'
            ) {
                $csvRow[$key] = 1;
            } elseif ($key == $activeKey && $value === '2') {
                $csvRow[$key] = 2;
            } elseif ($key == $activeKey && $value === '0') {
                $csvRow[$key] = 0;
            }
        }

        return $csvRow;
    }

    /**
     * @param string $key
     * @return int
     */
    protected function getIndexForKey(string $key): int
    {
        return array_search(
            $key,
            $this->getCsvHeaderFields()
        );
    }
}
