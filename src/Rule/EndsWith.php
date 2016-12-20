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
namespace Welhott\Vatlidator\Rule;

/**
 * Class EndsWithRule
 * @package Welhott\Vatlidator\Rule
 */
class EndsWith implements RuleInterface
{
    /**
     * @var string
     */
    private $value = '';

    /**
     * StartsWithRule constructor.
     * @param string $value The value that the string must end with for the validation to be true.
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * Checks is a given number/string ends in another number.
     * @param string $number The number to check.
     * @return bool True is the number contains the thing given in the constructor, false otherwise.
     *
     * TODO Perform a case-insensitive match?
     */
    public function validate(string $number) : bool
    {
        return substr_compare($number, $this->value, mb_strlen($this->value) * -1) === 0;
    }

    /**
     * Obtain the error configured by a rule.
     * @return string|null The error that this rule returns when the validation fails.
     */
    public function getError(): string
    {
        return '';
    }
}
