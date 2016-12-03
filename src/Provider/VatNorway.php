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

use Welhott\Vatlidator\VatProvider;

/**
 * Class VatNorway
 * @package Welhott\Vatlidator\Provider
 */
class VatNorway extends VatProvider
{
    /**
     * The ISO 3166-1 alpha-2 code that represents this country
     * @var string
     */
    private $country = 'NO';

    /**
     * The abbreviation of the VAT number according the the country's language.
     * @var string
     */
    private $abbreviation = 'Orgnr';

    /**
     * Each digit will be multiplied by a digit in this array in the equivalent position.
     * @var array
     */
    private $multipliers = [3, 2, 7, 6, 5, 4, 3, 2];

    /**
     *
     * @return bool True if the number is valid, false if it's not.
     * @see https://www.brreg.no/om-oss/samfunnsoppdraget-vart/registera-vare/einingsregisteret/organisasjonsnummeret/
     */
    public function validate() : bool
    {
        $controlDigit = intval(mb_substr($this->number, -1));
        $calculatedDigit = 0;

        if(!is_numeric($this->number)) {
            return false;
        }

        if(mb_strlen($this->number) !== 9) {
            return false;
        }

        for ($i = 0; $i < 8; $i++) {
            $calculatedDigit += $this->number[$i] * $this->multipliers[$i];
        }

        $remainder = $calculatedDigit % 11;
        $calculatedDigit = 11 - $remainder;

        if($remainder === 0) {
            $calculatedDigit = 0;
        } else if($remainder === 1) {
            $calculatedDigit = '-';
        }

        return $calculatedDigit === $controlDigit;
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
