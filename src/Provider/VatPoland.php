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
 * Class VatPoland
 * @package Welhott\Vatlidator\Provider
 */
class VatPoland extends VatProvider
{
    /**
     * The ISO 3166-1 alpha-2 code that represents this country
     * @var string
     */
    private $country = 'PL';

    /**
     * The abbreviation of the VAT number according the the country's language.
     * @var string
     */
    private $abbreviation = 'NIP';

    /**
     * Each digit will be multiplied by a digit in this array in the equivalent position.
     * @var array
     */
    private $multipliers = [6, 5, 7, 2, 3, 4, 5, 6, 7];

    /**
     * NOTE: This was a direct automated translation of the Polish Wikipedia article.
     *
     * The first three digits indicate the code of each NIP tax office, which gave the number. This code initially
     * there were only the digits from 1 to 9. In 2004, introduced dozens of new tax offices, was made an exception to
     * the existing rules and given new authorities codes with a zero in the second position.
     *
     * Thus, for example. Code 106 is Malopolska Tax Office in Krakow - given by him NIP 106-00-00-062 is correct. In
     * the past NIP normally it would write up, separating the groups of numbers link. For individuals grouped digits
     * 123-456-78-19, and for companies grouped 123-45-67-819. The company assumed by one person had NIP this person.
     * Currently it broadcast without hyphens.
     *
     * NIP tenth digit is a check digit that is calculated according to the following algorithm:
     * 1. Multiply each of the first nine digits respectively by weight of 6, 5, 7, 2, 3, 4, 5, 6, 7.
     * 2. Sum up the results of multiplication.
     * 3. Calculate the remainder of the division by 11 (modulo operation 11).
     *
     * NIP is so generated to never as a result of this division has not come out number 10. According to this
     * algorithm 000-000-00-00 number is correct, but it does not make sense. For the sequence of digits 123-456-78-90
     * you can not select a check digit to generate the correct VAT.
     *
     * @return bool True if the number is valid, false if it's not.
     * @see https://pl.wikipedia.org/wiki/NIP
     */
    public function validate() : bool
    {
        $total = 0;

        if(!is_numeric($this->cleanNumber)) {
            return false;
        }

        if(mb_strlen($this->cleanNumber) !== 10) {
            return false;
        }

        for ($i = 0; $i < 9; $i++) {
            $total += $this->cleanNumber[$i] * $this->multipliers[$i];
        }

        $total = (($total % 11) > 9) ? 0 : ($total % 11);
        return $total === $this->getCheckDigit();
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
