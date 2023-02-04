<?php
/**
 * Durst - project - GlnValidator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 02.12.21
 * Time: 09:35
 */

namespace Pyz\Zed\Merchant\Business\Code;

use Pyz\Zed\Merchant\Business\Exception\Code\GlnChecksumMismatchException;
use Pyz\Zed\Merchant\Business\Exception\Code\GlnInvalidException;
use Pyz\Zed\Merchant\Business\Exception\Code\GlnStringLengthException;

class GlnValidator implements GlnValidatorInterface
{
    protected const GLN_LENGTH = 13;
    protected const GLN_FIRST_MULTIPLIER = 3;
    protected const GLN_SECOND_MULTIPLIER = 1;
    protected const GLN_MODULO = 10;

    /**
     * {@inheritDoc}
     *
     * @param string $gln
     * @return bool
     */
    public function validate(string $gln): bool
    {
        try {
            $this->checkLength($gln);
            $this->checkChecksum($gln);

            return true;
        } catch (GlnInvalidException $exception){
            return false;
        }
    }

    /**
     * @param string $gln
     */
    protected function checkLength(string $gln)
    {
        if (strlen($gln) !== static::GLN_LENGTH) {
            throw new GlnStringLengthException(
                sprintf(
                    GlnStringLengthException::MESSAGE,
                    static::GLN_LENGTH
                )
            );
        }
    }

    /**
     * @param string $gln
     */
    protected function checkChecksum(string $gln)
    {
        $glnChars = $this->glnStringToReversedCharArray($gln);

        $checksum = (int) array_shift($glnChars);

        $currentMultiplier = static::GLN_FIRST_MULTIPLIER;
        $computedChecksum = 0;
        foreach ($glnChars as $glnChar) {
            $glnNumber = (int) $glnChar;
            $computedChecksum += $glnNumber * $currentMultiplier;

            if ($currentMultiplier === static::GLN_FIRST_MULTIPLIER){
                $currentMultiplier = static::GLN_SECOND_MULTIPLIER;
            } else {
                $currentMultiplier = static::GLN_FIRST_MULTIPLIER;
            }
        }

        $computedChecksum = static::GLN_MODULO - ($computedChecksum % static::GLN_MODULO);

        if ($checksum !== $computedChecksum){
            throw new GlnChecksumMismatchException(
                GlnChecksumMismatchException::MESSAGE
            );
        }
    }

    /**
     * @param string $gln
     * @return array
     */
    protected function glnStringToReversedCharArray(string $gln) : array
    {
        $glnChars = str_split($gln);
        $glnChars = array_reverse($glnChars);

        return $glnChars;
    }
}
