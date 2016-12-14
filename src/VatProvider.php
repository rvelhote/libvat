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
        $this->cleanNumber = $this->clean($number);
        $this->cleaners = [new Trim(), new Uppercase(), new ExtraCharacters(), new Country()] + $cleaners;
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
}
