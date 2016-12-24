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

use Welhott\Vatlidator\Normalizer\Country;
use Welhott\Vatlidator\Normalizer\ExtraCharacters;
use Welhott\Vatlidator\Normalizer\NormalizerInterface;
use Welhott\Vatlidator\Normalizer\Trim;
use Welhott\Vatlidator\Normalizer\Uppercase;

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
     * @var NormalizerInterface[]
     */
    private $normalizers = [];

    /**
     * VatProvider constructor.
     * @param string $number
     * @param NormalizerInterface[] $normalizers
     */
    public function __construct(string $number, array $normalizers = [])
    {
        $this->number = $number;

        $defaultNormalizers = [new Trim(), new Uppercase(), new ExtraCharacters(), new Country()];
        $this->normalizers = array_merge($defaultNormalizers, $normalizers);

        $this->cleanNumber = $this->clean($number);
    }

    /**
     * @param string $number
     * @return string
     */
    protected function clean(string $number) : string
    {
        $callback = function(string $number, NormalizerInterface $transformer) {
            return $transformer->normalize($number);
        };

        return array_reduce($this->normalizers, $callback, $number);
    }

    /**
     * @param string $pattern
     * @return bool
     */
    protected function matchesPattern(string $pattern) : bool
    {
        return preg_match('/^'.$pattern.'$/', $this->cleanNumber) === 1;
    }

    /**
     * @return bool
     */
    public abstract function validate() : bool;

    /**
     * Returns the check digit of a number. Use this when the check digit is only numbers.
     * @param int $position The starting position of the check digit (from the end)
     * @param int $size The length of the check digit.
     * @return int Returns the check digit of the number.
     */
    protected function getCheckDigit(int $position = 1, int $size = 0) : int {
        if($size === 0) {
            return intval(mb_substr($this->cleanNumber, abs($position) * -1));
        }
        return intval(mb_substr($this->cleanNumber, abs($position) * -1, $size));
    }

    /**
     * Returns the check character of a number. Use this when the check digit is a letter or alphanumeric.
     * @param int $position How many characters is the check character.
     * @param int $size The length of the check character.
     * @return string Returns the check digit of the number.
     */
    protected function getCheckChar(int $position = 1, int $size = 1) : string {
        return mb_substr($this->cleanNumber, abs($position) * -1, $size);
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
