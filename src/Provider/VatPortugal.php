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
 * Class VatPortugal
 * @package Welhott\Vatlidator\Provider
 */
class VatPortugal extends VatProvider
{
    /**
     * The ISO 3166-1 alpha-2 code that represents this country.
     * @var string
     */
    private $country = 'PT';

    /**
     * The list of valid first digits according to the portuguese NIF rules.
     * @var array
     */
    private $validFirstDigits = [1, 2, 5, 6, 7, 8, 9];

    /**
     * The list of valid multipliers for the validation algorithm.
     * @var array
     */
    private $multipliers = [9, 8, 7, 6, 5, 4, 3, 2];

    /**
     * NOTE: This was a direct automated translation of the Portuguese Wikipedia article.
     *
     * It consists of nine digits, the first eight being sequential and the last one being a check digit.
     *
     * The NIF may belong to one of several ranges of numbers, defined by the initial digits, with the following
     * interpretations [1]:
     * - 1 to 3: Natural person, 3 is not yet allocated
     * - 45: Person singular. The initial figures "45" correspond to non-resident citizens who only obtain income in
     * Portugal definitively subject to withholding tax.
     * - 5: legal person required to register in the National Registry of Legal Persons; [3]
     * - 6: Organization of Central, Regional or Local Public Administration;
     * - 70, 74 and 75: Inheritance Indivisa, in which the successor was not an individual entrepreneur, or Indivisa
     * Inheritance in which the surviving spouse has commercial income;
     * - 71: Non-resident residents subject to final withholding tax.
     * - 72: Investment funds.
     * - 77: Informal allocation of taxpayer NIF (entities that do not require NIF with RNPC).
     * - 78: Informal allocation to non-residents covered by VAT REFUND.
     * - 79: Exceptional regime - Expo 98.
     * - 8: "sole proprietorship" (no longer used, no longer valid);
     * - 90 and 91: Condos, Irregular Companies, Indivisible Inheritances whose successor was an individual entrepreneur.
     * - 98: Non-residents without a permanent establishment.
     * - 99: Civil partnerships without legal personality.
     *
     * The ninth and last digit is the control digit. It is calculated using the module 11 algorithm.
     *
     * @param string $number The VAT number to process
     * @see https://pt.wikipedia.org/wiki/N%C3%BAmero_de_identifica%C3%A7%C3%A3o_fiscal
     */
    public function __construct(string $number)
    {
        parent::__construct($number);
    }

    /**
     * NOTE: This was a direct automated translation of the Portuguese Wikipedia article.
     *
     * The NIF has 9 digits, the last one being the control digit. To calculate the control digit:
     * 1. Multiply the 8th digit by 2, the 7th digit by 3, the 6th digit by 4, the 5th digit by 5, the 4th digit by 6,
     * the 3rd digit by 7, the 2nd digit by 8, and 1st digit by 9
     * 2. Add results
     * 3. Compute Module 11 of the result, that is, the remainder of the division of the number by 11.
     *
     * If the remainder is 0 or 1, the control digit will be 0
     * If it is another digit x, the control digit will be the result of 11 - x
     *
     * @see https://pt.wikipedia.org/wiki/N%C3%BAmero_de_identifica%C3%A7%C3%A3o_fiscal
     */
    public function validate() : bool
    {
        $calculatedCheckDigit = 0;

        $firstDigit = intval(substr($this->number, 0, 1));
        $checkDigit = intval(substr($this->number, -1));

        if (!is_numeric($this->number) || strlen($this->number) !== 9) {
            return false;
        }

        if(!in_array($firstDigit, $this->validFirstDigits)) {
            return false;
        }

        for($i = 0; $i < 8; $i++) {
            $calculatedCheckDigit += $this->number[$i] * $this->multipliers[$i];
        }

        $calculatedCheckDigit = 11 - ($calculatedCheckDigit % 11);
        $calculatedCheckDigit = ($calculatedCheckDigit >= 10) ? 0 : $calculatedCheckDigit;

        return $calculatedCheckDigit === $checkDigit;
    }

    /**
     * Obtain the country code that represents this country.
     * @return string An ISO 3166-1 alpha-2 code that represents this country.
     */
    public function getCountry() : string
    {
        return $this->country;
    }
}
