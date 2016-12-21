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
use Welhott\Vatlidator\Rule\BasicRuleset;
use Welhott\Vatlidator\Rule\IsNumeric;
use Welhott\Vatlidator\Rule\Italy\ContainsTaxOffice;
use Welhott\Vatlidator\Rule\LengthEquals;
use Welhott\Vatlidator\Rule\NotStartsWith;
use Welhott\Vatlidator\VatProvider;

/**
 * Class VatItaly
 * @package Welhott\Vatlidator\Provider
 */
class VatItaly extends VatProvider
{
    use DigitalRoot;

    /**
     * The ISO 3166-1 alpha-2 code that represents this country
     * @var string
     */
    private $country = 'IT';

    /**
     * The abbreviation of the VAT number according the the country's language.
     * @var string
     */
    private $abbreviation = 'P.IVA';

    /**
     * The list of valid multipliers for the validation algorithm.
     * @var array
     */
    private $multipliers = [1, 2, 1, 2, 1, 2, 1, 2, 1, 2];

    /**
     * The Italian USt IdNr must the following conditions:
     *  - x1-7 may not be 000000
     *  - y1-3 = 001-100, 120, 121
     *
     * the first seven digits represent the serial number of the person assigned by the relevant provincial office,
     * which is obtained by increasing the number of units assigned to the subject that precedes it.  the figures from
     * the eighth to the tenth indicate the provincial office of the IRS code that issued the freshman, generally
     * corresponding to the code ISTAT of the province.  the eleventh digit, finally, is a control code, introduced in
     * order to verify the correctness of the first ten digits.
     *
     * @return bool True if the number is valid, false if it's not.
     * @see http://zylla.wipos.p.lodz.pl/ut/translation.html
     * @see https://it.wikipedia.org/wiki/Partita_IVA
     * @see http://www.riolab.org/index.php?option=com_content&view=article&id=55&
     * @see http://aino.it/algoritmi-calcolo-partita-iva/
     */
    public function validate() : bool
    {
        $rules = [new IsNumeric(), new LengthEquals(11), new NotStartsWith('000000'), new ContainsTaxOffice()];
        $basicRules = new BasicRuleset($this->cleanNumber, $rules);

        if($basicRules->valid() === false) {
            return false;
        }

        $checksum = 0;

        for($i = 0; $i < 10; $i++) {
            $checksum += $this->digitalRoot($this->cleanNumber[$i] * $this->multipliers[$i]);
        }

        $checksum = 10 - ($checksum % 10);

        if($checksum === 10) {
            $checksum = 0;
        }

        return $checksum === $this->getCheckDigit();
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
