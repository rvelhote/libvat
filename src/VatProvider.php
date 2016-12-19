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
namespace Welhott\Vatlidator;
use Welhott\Vatlidator\Cleaner\Country;
use Welhott\Vatlidator\Cleaner\ExtraCharacters;
use Welhott\Vatlidator\Cleaner\CleanerInterface;
use Welhott\Vatlidator\Cleaner\Trim;
use Welhott\Vatlidator\Cleaner\Uppercase;

/**
 * Interface VatProviderInterface
 * @package Welhott\Vatlidator
 */
abstract class VatProvider
{
    /**
     * The VAT number as a string because multiple providers have letters and also it's easier to work with the number.
     * @var string
     */
    protected $number;

    /**
     * @var string
     */
    protected $cleanNumber;

    /**
     * @var CleanerInterface[]
     */
    private $cleaners = [];

    /**
     * VatProvider constructor.
     * @param string $number
     * @param CleanerInterface[] $cleaners
     */
    public function __construct(string $number, array $cleaners = [])
    {
        $this->number = $number;
        $this->cleaners = [new Trim(), new Uppercase(), new ExtraCharacters(), new Country()];
        $this->cleanNumber = $this->clean($number);
    }

    /**
     * @param string $number
     * @return string
     */
    protected function clean(string $number) : string
    {
        $callback = function(string $number, CleanerInterface $transformer) {
            return $transformer->transform($number);
        };

        return array_reduce($this->cleaners, $callback, $number);
    }

    /**
     * @return bool
     */
    public abstract function validate() : bool;

    /**
     * Returns the check digit of a number. Use this when the check digit is only numbers.
     * @param int $length How many characters is the check digit.
     * @return int Returns the check digit of the number.
     */
    protected function getCheckDigit(int $length = 1) : int {
        return intval(mb_substr($this->cleanNumber, abs($length) * -1));
    }

    /**
     * Returns the check character of a number. Use this when the check digit is a letter or alphanumeric.
     * @param int $length How many characters is the check character.
     * @return string Returns the check digit of the number.
     */
    protected function getCheckChar(int $length = 1) : string {
        return mb_substr($this->cleanNumber, abs($length) * -1);
    }

    /**
     * @return string
     */
    public function getNumber() : string
    {
        return $this->number;
    }

    /**
     * Obtain the country code that represents this country.
     * @return string An ISO 3166-1 alpha-2 code that represents this country.
     */
    public abstract function getCountry() : string;

    /**
     * @return string
     */
    public abstract function getAbbreviation() : string;

    /**
     * Returns the number with a certain formatting. The way it works is analogous to the date() function. Each letter
     * has a special meaning. The letters that are currently available are:
     * - c: Replaced by the country
     * - n: Replaced by the CLEAN number
     * - a: Replaced by the abbreviation
     *
     * @param string $format The format in which we want the number returned.
     * @return string A formatted number according to the variable passed as parameter.
     *
     * TODO Implement escape strings? Would it be worth it?
     */
    public function format(string $format = 'cn') : string {
        $formatters = ['c', 'n', 'a'];
        $values = [$this->getCountry(), $this->cleanNumber, $this->getAbbreviation()];
        return str_replace($formatters, $values, $format);
    }
}
