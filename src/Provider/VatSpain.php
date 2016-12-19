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
 * Class VatSpain
 * @package Welhott\Vatlidator\Provider
 * @see https://es.wikipedia.org/wiki/N%C3%BAmero_de_identificaci%C3%B3n_fiscal
 */
class VatSpain extends VatProvider
{
    /**
     * The ISO 3166-1 alpha-2 code that represents this country
     * @var string
     */
    private $country = 'ES';

    /**
     * The abbreviation of the VAT number according the the country's language. This abbreviation represents people.
     * @var string
     */
    private $abbreviation = 'NIP';

    /**
     * The abbreviation of the VAT number according the the country's language. This abbreviation represents companies.
     * @var string
     */
    private $abbreviationCompanies = 'CIF';

    /**
     * The list of valid control characters for the VAT number.
     * Letters such as I, Ñ, O, U are discarded to avoid confusion with other similar character (e.g. N, 1, l).
     * @var string
     */
    private $chars = 'TRWAGMYFPDXBNJZSQVHLCKE';

    /**
     * To calculate the control character for legal entities and entities in general numbers in the even position of
     * the VAT (attention: not the number itself, the position) are multiplied by 2 while the numbers in the odd
     * position do not have any calculation (hence the multiplication by 1).
     * @var array
     */
    private $multipliers = [2, 1, 2, 1, 2, 1, 2];

    /**
     * The spanish VAT number has a few different patterns for its number depending if you are a person or a company.
     * These are the possible patterns that trigger a different form of validation.
     * @var array
     */
    private $patterns = [
        '/^[A-H|J|U|V]\d{8}$/',
        '/^[A-H|N-S|W]\d{7}[A-J]$/',
        '/^[0-9|K|L|M|X|Y|Z]\d{7}[A-Z]$/',
    ];

    /**
     * VatSpain constructor.
     * @param string $number
     */
    public function __construct(string $number)
    {
        parent::__construct($number);
    }

    /**
     * Validate a spanish VAT number.
     *
     * There are three types VAT numbers in Spain:
     *  1. A national entity/company
     *  2. A foreign entity/company
     *  3. A person (further divided into citizens, foreigners, children without ID and temporary numbers)
     *
     * These three types has its own way of validating the number.
     *
     * @see VatSpain::validatePersonalEntity()
     * @see VatSpain::validateNationalJuridicEntity()
     * @see VatSpain::validateNonNationalJuridicEntity()
     */
    public function validate() : bool
    {
        if (preg_match($this->patterns[0], $this->number) === 1) {
            return $this->validateNationalJuridicEntity();
        }

        if (preg_match($this->patterns[1], $this->number) === 1) {
            return $this->validateNonNationalJuridicEntity();
        }

        if (preg_match($this->patterns[2], $this->number) === 1) {
            return $this->validatePersonalEntity();
        }

        return false;
    }

    /**
     * @return bool
     */
    private function validateNationalJuridicEntity() : bool
    {
        $total = 10 - $this->calculateControlChar() % 10;

        if ($total == 10) {
            $total = 0;
        }

        return $total === $this->getCheckDigit();
    }

    /**
     * @return bool
     */
    private function validateNonNationalJuridicEntity() : bool
    {
        $total = chr((10 - $this->calculateControlChar() % 10) + 64);
        return $total === $this->getCheckChar();
    }

    /**
     * Validate a number belonging to a person. A number belonging to a person is composed by a letter that has a
     * certain meaning, seven number and finally a control digit.
     *
     * Example: X7260599M
     *  X - Type of VAT
     *  7260599 - The number to validate
     *  M - The control char
     *
     * The meaning of the first letter is the following:
     * - NIF K: Spaniards under 14 years old who do not have a spanish ID.
     * - NIF L: Spaniards over 14 years of age residing abroad and who do not have an ID card that travels to Spain
     * for less than six months.
     * - NIF M: Assigned to foreigners temporarily while the definitive number is not assigned.
     * - NIE X: Foreigners resident in Spain and identified by law enforcement authorities with an ID number. This
     * letter was assigned from 1997 to 2008.
     * - NIE Y: Foreigners resident in Spain and identified by law enforcement authorities with an ID number. Assigned
     * after 2008.
     * - NIE Z: Reserved letter used for foreigners identified by law enforcement authorities when there are no more
     * possible Y combinations.
     *
     * If the number starts with a K, L or M we ignore that identifier and use only the numbers and the control char.
     * If the number starts with a X, Y or a Z, it's replaced by a 0, 1 or 2 respectively.
     *
     * We then use the modulus operation in the middle part of the number. The calculation is NUMBER % 23 and the
     * result of this calculation is looked-up in the following table. The calculated value from the modulus operation
     * has to match a letter on the table and that letter has to the equal to the control letter in the number (the
     * last letter in the number)
     *
     * 0  1  2  3  4  5  6  7  8  9  10  11  12  13  14  15  16  17  18  19  20  21  22
     * T  R  W  A  G  M  Y  F  P  D  X   B   N   J   Z   S   Q   V   H   L   C   K   E
     *
     * @return bool True if the number of valid, false otherwise.
     */
    private function validatePersonalEntity() : bool
    {
        $foreigner = [
            'X' => '0',
            'Y' => '1',
            'Z' => '2',
        ];

        $child = [
            'K',
            'L',
        ];

        $transitory = [
            'M',
        ];

        $number = $this->number;
        $firstChar = $this->number[0];
        $number[0] = array_key_exists($firstChar, $foreigner) ? $foreigner[$firstChar] : $firstChar;

        if(in_array($firstChar, $child) || in_array($firstChar, $transitory)) {
            $number = mb_substr($this->number, 1);
        }

        $partial = mb_substr($number, 0, -1);
        return isset($this->chars[$partial % 23]) && $this->getCheckChar() == $this->chars[$partial % 23];
    }

    /**
     * Internal calculation of the control char of a given VAT number according to spanish rules.
     *
     * Numbers for companies and/or legal entities are composed of one letter in the beginning, one letter in the end
     * and numbers in the middle. Only the middle part (i.e. the numbers) are considered for this calculation.
     *
     * There are 7 numbers in total. The numbers in the even position are multiplied by two and the numbers in the odd
     * position have no calculation done upon them.
     *
     * @return int The total sum of the middle VAT number part taking into account multipliers in the even position.
     */
    private function calculateControlChar()
    {
        $total = 0;
        $centralNumber = mb_substr($this->number, 1, 7);

        for ($i = 0; $i < 7; $i++) {
            $temp = $centralNumber[$i] * $this->multipliers[$i];

            if ($temp > 9) {
                $total += floor($temp / 10) + $temp % 10;
            } else {
                $total += $temp;
            }
        }

        return $total;
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
     * TODO The designation is different for persons and companies.
     */
    public function getAbbreviation() : string
    {
        return $this->abbreviation;
    }
}
