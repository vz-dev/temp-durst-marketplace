<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-03-07
 * Time: 16:29
 */

namespace Pyz\Zed\Discount;

use Generated\Shared\Transfer\SequenceNumberSettingsTransfer;
use Spryker\Shared\Kernel\Store;
use Spryker\Zed\Discount\DiscountConfig as SprykerDiscountConfig;

class DiscountConfig extends SprykerDiscountConfig
{
    protected const DEFAULT_DISCOUNT_DISPLAY_NAME = 'Discount';
    protected const DEFAULT_DISCOUNT_DISPLAY_PREFIX = 'DISCOUNT';

    protected const DEFAULT_CART_DISCOUNT_GROUP_NAME = 'CartDiscountGroup';
    protected const DEFAULT_CART_DISCOUNT_GROUP_PREFIX = 'CARTDISCOUNTGROUP';

    /**
     * @return SequenceNumberSettingsTransfer
     */
    public function getDiscountDisplayNameDefaults(): SequenceNumberSettingsTransfer
    {
        $sequenceNumberSettingsTransfer = new SequenceNumberSettingsTransfer();

        $name = static::DEFAULT_DISCOUNT_DISPLAY_NAME . $this->getUniqueIdentifierSeparator() . '%s';

        $prefixes[] = Store::getInstance()->getStoreName();
        $prefixes[] = static::DEFAULT_DISCOUNT_DISPLAY_PREFIX;
        $prefixes[] = '%s';

        $prefix = implode($this->getUniqueIdentifierSeparator(), $prefixes) . $this->getUniqueIdentifierSeparator();

        $sequenceNumberSettingsTransfer
            ->setName($name)
            ->setPrefix($prefix);

        return $sequenceNumberSettingsTransfer;
    }

    /**
     * @return \Generated\Shared\Transfer\SequenceNumberSettingsTransfer
     */
    public function getCartDiscountGroupNameDefaults(): SequenceNumberSettingsTransfer
    {
        $sequenceNumberSettingsTransfer = new SequenceNumberSettingsTransfer();

        $name = static::DEFAULT_CART_DISCOUNT_GROUP_NAME . $this->getUniqueIdentifierSeparator() . '%s';

        $prefixes[] = Store::getInstance()->getStoreName();
        $prefixes[] = static::DEFAULT_CART_DISCOUNT_GROUP_PREFIX;
        $prefixes[] = '%s';

        $prefix = implode(
            $this->getUniqueIdentifierSeparator(),
            $prefixes
        ) . $this->getUniqueIdentifierSeparator();

        $sequenceNumberSettingsTransfer
            ->setName($name)
            ->setPrefix($prefix);

        return $sequenceNumberSettingsTransfer;
    }

    /**
     * @return string
     */
    protected function getUniqueIdentifierSeparator(): string
    {
        return '-';
    }

    /**
     * @return array
     */
    public function getVoucherCodeCharacters()
    {
        return [
            self::KEY_VOUCHER_CODE_CONSONANTS => [
                'B', 'C', 'D', 'F', 'G', 'H', 'J', 'K', 'L', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'X', 'Y', 'Z',
            ],
            self::KEY_VOUCHER_CODE_VOWELS => [
                'A', 'E', 'U',
            ],
            self::KEY_VOUCHER_CODE_NUMBERS => [
                1, 2, 3, 4, 5, 6, 7, 8, 9,
            ],
        ];
    }
}
