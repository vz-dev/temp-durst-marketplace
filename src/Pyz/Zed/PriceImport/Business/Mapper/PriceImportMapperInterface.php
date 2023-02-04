<?php
/**
 * Durst - project - PriceImportMapperInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 05.10.20
 * Time: 11:16
 */

namespace Pyz\Zed\PriceImport\Business\Mapper;


interface PriceImportMapperInterface
{
    /**
     * Get the identifier / name of the mapper
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Return the header fields for this mapping process
     *
     * @return array
     */
    public function getCsvHeaderFields(): array;

    /**
     * Get the delimiter used for the chosen mapper
     *
     * @return string
     */
    public function getImportDelimiter(): string;

    /**
     * Gets a row from the CSV and transforms it into a valid array
     * For the price transfer creation
     *
     * @param array $csvRow
     * @return array
     */
    public function getMappedRow(array $csvRow): array;

    /**
     * Return the field (index) where the SKU for the merchant is stored
     *
     * @return int
     */
    public function getMerchantSkuIndex(): int;

    /**
     * Return the field (index) where the gross price for the merchant is stored
     *
     * @return int
     */
    public function getGrossPriceIndex(): int;
}
