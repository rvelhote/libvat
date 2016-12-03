<?php
/*
 * The MIT License (MIT)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
namespace Welhott\Vatlidator\Provider;

use Welhott\Vatlidator\CalculationTrait\DigitalRoot;
use Welhott\Vatlidator\VatProvider;

/**
 * Class VatAustria
 * @package Welhott\Vatlidator\Provider
 */
class VatAustria extends VatProvider
{
    use DigitalRoot;

    /**
     * The ISO 3166-1 alpha-2 code that represents this country
     * @var string
     */
    private $country = 'AT';

    /**
     * The abbreviation of the VAT number according the the country's language.
     * @var string
     */
    private $abbreviation = 'UID';

    /**
     * @var int
     */
    const LENGTH = 9;

    /**
     * @var int
     */
    const MODULUS = 10;

    /**
     * Each digit will be multiplied by a digit in this array in the equivalent position.
     * @var array
     */
    private $multipliers = [1, 2, 1, 2, 1, 2, 1];

    /**
     *
     * @return bool True if the number is valid, false if it's not.
     *
     * @see http://zylla.wipos.p.lodz.pl/ut/translation.html
     * @see https://en.wikipedia.org/wiki/Digital_root
     */
    public function validate() : bool
    {
        $checkDigit = intval(mb_substr($this->number, -1));
        $calculatedCheckDigit = 0;

        if (mb_strlen($this->number) !== self::LENGTH) {
            return false;
        }

        if ($this->number[0] !== 'U') {
            return false;
        }

        // Exclude the first char and the last number which is the check digit.
        for ($i = 1, $j = 0; $i <= self::LENGTH - 2; $i++, $j++) {
            $calculatedCheckDigit += $this->digitalRoot($this->number[$i] * $this->multipliers[$j]);
        }

        $calculatedCheckDigit = (96 - $calculatedCheckDigit) % self::MODULUS;
        return $checkDigit === $calculatedCheckDigit;
    }

    /**
     * Obtain the country code that represents this country.
     * @return string An ISO 3166-1 alpha-2 code that represents this country.
     */
    public function getCountry() : string
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function getAbbreviation() : string
    {
        return $this->abbreviation;
    }
}
