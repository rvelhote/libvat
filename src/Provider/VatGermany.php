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
 * Class VatGermany
 * @package Welhott\Vatlidator\Provider
 */
class VatGermany extends VatProvider
{
    /**
     * The ISO 3166-1 alpha-2 code that represents this country
     * @var string
     */
    private $country = 'DE';

    /**
     * The abbreviation of the VAT number according the the country's language.
     * @var string
     */
    private $abbreviation = 'USt-IdNr.';

    /**
     *
     * @return bool True if the number is valid, false if it's not.
     * @see http://zylla.wipos.p.lodz.pl/ut/translation.html
     */
    public function validate() : bool
    {
        if(mb_strlen($this->cleanNumber) !== 9) {
            return false;
        }

        if(!is_numeric($this->cleanNumber)) {
            return false;
        }

        $product = 10;

        for($i = 0; $i <  8; $i++) {
            $sum = ($this->cleanNumber[$i] + $product) % 10;

            if($sum == 0) {
                $sum = 10;
            }

            $product = (2 * $sum) % 11;
        }

        $calculatedCheckDigit = 11 - $product;

        if($calculatedCheckDigit == 10) {
            $calculatedCheckDigit = 0;
        }

        return $calculatedCheckDigit === $this->getCheckDigit();
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
