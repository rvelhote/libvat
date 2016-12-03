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
 * Class VatCanada
 * @package Welhott\Vatlidator\Provider
 */
class VatCanada extends VatProvider
{
    /**
     * The ISO 3166-1 alpha-2 code that represents this country
     * @var string
     */
    private $country = 'CA';

    /**
     * The abbreviation of the VAT number according the the country's language.
     * @var string
     */
    private $abbreviation = 'BN';

    /**
     * Each digit will be multiplied by a digit in this array in the equivalent position.
     * @var array
     */
    private $multipliers = [3, 2, 7, 6, 5, 4, 3, 2];

    /**
     *
     * @return bool True if the number is valid, false if it's not.
     * @see http://www.metca.com/products/sin-bn-validator
     */
    public function validate() : bool
    {
        $bn = $this->number;
        // function to validate a Canadian CRA Busines Number (BN)
        // Note: same checkdigit formula as SIN validation applies to BN

        // initialize validation variable to true
        $valid = true;
        $msg = "";

        // trim whitespace at beginning and end of string
        //bn = bn.trim();

        // trim extra whitespace from string
        // Note: this function will trim ALL whitespace from string vs prev function which only trimmed
        // from beginning and end of string.
        // Requires trim() function in genfunc.js to be included as external script file
        $bn = trim($bn);

        // Remove non-numeric characters.
        // Note: Only use this if we want to fix input errors without alerting user to the error.  Probably not a useful thing
        // to do since the string probably won't pass the required test for 9 characters or the checkdigit - better to alert the
        // user to the fact that they input non-numeric data.
        // bn = numericOnly(bn);	// function (will work in v3 browsers)
        // bn = bn.replace(/(\D)+/g,"");	// (will not work in v3 browsers)

        // must be 9 characters (digits)
        $digits = mb_strlen($bn);
        if ($digits != 9) {
            $valid = false;
        } else {
            if (preg_match('/^\d+$/', $bn) !== 1)    // must contain ONLY digits 0-9
            {
                $valid = false;
            } else {
                if ($bn == "000000000")        // for use when unknown SIN only
                {
                    $msg = "000000000 may be used only when BN is unknown - please revalidate when BN is available";
                } else {    // perform the checkdigit validation routine

                    // last (9th) digit is the check digit
                    $checkdigit = mb_substr($bn, -1);

                    // Double the even-numbered position digits (pos 2,4,6 & 8)
                    $double2 = intval($bn[1]) * 2;
                    $double4 = intval($bn[3]) * 2;
                    $double6 = intval($bn[5]) * 2;
                    $double8 = intval($bn[7]) * 2;

                    // concatenate the doubles into one number string
                    $num1 = $double2 . $double4 . $double6 . $double8;

                    // Extract the odd-numbered position digits
                    $digit1 = $bn[0];
                    $digit3 = $bn[2];
                    $digit5 = $bn[4];
                    $digit7 = $bn[6];

                    // concatenate the digits into one number string
                    $num2 = $digit1 . $digit3 . $digit5 . $digit7;

                    // sum the digits in num1
                    $crossadd1 = 0;
                    $position = 0;
                    for ($position = 0; $position < strlen($num1); $position++) {
                        $crossadd1 = $crossadd1 + intval(substr($num1, $position, 1));
                    }

                    // sum the digits in num2
                    $crossadd2 = 0;
                    for ($position = 0; $position < strlen($num2); $position++) {
                        $crossadd2 = $crossadd2 + intval(substr($num2, $position, 1));
                    }

                    // add the two sums
                    $checksum1 = $crossadd1 + $crossadd2;
                    $checksum2 = 0;
                    $checkdigitX = 0;

                    if (substr($checksum1, strlen($checksum1) - 1) == "0") {
                        $checksum2 = $checksum1;
                        $checkdigitX = '0';
                    } else {
                        $checksum2 = (ceil($checksum1 / 10.0) * 10.0);
                        $checkdigitX = floatval($checksum2 - $checksum1);
                    }

                    if ($checkdigitX == $checkdigit) {
                        $valid = true;
                    } else {
                        $valid = false;
                    }

                }
            }
        }

        return $valid;
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
