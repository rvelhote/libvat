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
namespace Welhott\Vatlidator\CalculationTrait;

/**
 * Trait DigitalRoot
 * @package Welhott\Vatlidator
 */
trait DigitalRoot {
    /**
     * Calculates the digital root of a number. Basically, a digital root is the sum of all digits in a number.
     *
     * 12 = 1 + 2 = 3 (the digital root is 3)
     * 77 = 7 + 7 = 14 (the digital root is 14)
     *
     * @param int $number The number that we wish to calculate the digital root for
     * @return int The calculated digital root
     *
     * @see https://en.wikipedia.org/wiki/Digital_root
     */
    public function digitalRoot(int $number) : int
    {
        return ($number % 9 == 0 && $number != 0) ? 9 : $number % 9;
    }
}
