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
     * @var string
     */
    private $country = 'PT';

    /**
     * Portugal constructor.
     * @param string $number
     */
    public function __construct(string $number)
    {
        parent::__construct($number);
    }

    /**
     *
     */
    public function validate() : bool
    {
        if (!is_numeric($this->number) || strlen($this->number)!=9) {
            return false;
        }

        $nifSplit = str_split($this->number);

        if (in_array($nifSplit[0], array(1, 2, 5, 6, 7, 8, 9))) {
            $checkDigit=0;
            for($i=0; $i<8; $i++) {
                $checkDigit+=$nifSplit[$i]*(10-$i-1);
            }
            $checkDigit=11-($checkDigit % 11);

            if($checkDigit>=10) $checkDigit=0;

            if ($checkDigit==$nifSplit[8]) {
                return true;
            } else {
                return false;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getCountry() : string
    {
        return $this->country;
    }
}
