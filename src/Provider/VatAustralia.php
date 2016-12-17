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
 * Class VatAustralia
 * @package Welhott\Vatlidator\Provider
 */
class VatAustralia extends VatProvider
{
    /**
     * The ISO 3166-1 alpha-2 code that represents this country
     * @var string
     */
    private $country = 'AU';

    /**
     * The abbreviation of the VAT number according the the country's language.
     * @var string
     */
    private $abbreviation = 'ABN';

    /**
     * The list of valid multipliers for the validation algorithm. These multipliers are specific to the ABN.
     * @var array
     */
    private $abnMultipliers = [10, 1, 3, 5, 7, 9, 11, 13, 15, 17, 19];

    /**
     * The list of valid multipliers for the validation algorithm. These multipliers are specific to the TFN.
     * @var array
     */
    private $tfnNultipliers = [1, 4, 3, 7, 5, 8, 6, 9, 10];

    /**
     *
     * @return bool True if the number is valid, false if it's not.
     * @see https://abr.business.gov.au/HelpAbnFormat.aspx
     */
    public function validate() : bool
    {
        if(!is_numeric($this->cleanNumber)) {
            return false;
        }

        if(mb_strlen($this->cleanNumber) !== 11 && mb_strlen($this->cleanNumber) !== 9) {
            return false;
        }


        if(mb_strlen($this->cleanNumber) === 11) {
            return $this->validateAbn();
        }

        return $this->validateTfn();
    }

    /**
     * For companies it is called ABN (Australina Business Number)
     * @return bool
     */
    private function validateAbn() {
        $number = $this->cleanNumber;
        $number[0] = intval($number[0]) - 1;
        $calculatedCheckDigit = 0;

        for($i = 0; $i < 11; $i++) {
            $calculatedCheckDigit += $number[$i] * $this->abnMultipliers[$i];
        }

        $calculatedCheckDigit = $calculatedCheckDigit % 89;
        return $calculatedCheckDigit === 0;
    }

    /**
     * For individuals is called TFN (Tax File Number)
     * @see https://en.wikipedia.org/wiki/Tax_file_number
     */
    private function validateTfn() {
        $calculatedCheckDigit = 0;

        for($i = 0; $i < 9; $i++) {
            $calculatedCheckDigit += $this->cleanNumber[$i] * $this->tfnNultipliers[$i];
        }

        $calculatedCheckDigit = $calculatedCheckDigit % 11;
        return $calculatedCheckDigit === 0;
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
