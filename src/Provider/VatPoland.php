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
     * @var string
     */
    private $country = 'PL';

    /**
     * @var array
     */
    private $multipliers = [6, 5, 7, 2, 3, 4, 5, 6, 7];

    /**
     * @return bool
     */
    public function validate() : bool
    {
        $controlChar = intval(mb_substr($this->number, -1));
        $total = 0;

        for ($i = 0; $i < 9; $i++) {
            $total += $this->number[$i] * $this->multipliers[$i];
        }

        $total = (($total % 11) > 9) ? 0 : ($total % 11);
        return $total === $controlChar;
    }

    /**
     * @return string
     */
    public function getCountry() : string
    {
        return $this->country;
    }
}
