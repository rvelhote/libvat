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
namespace Welhott\Vatlidator\Cleaner;

/**
 * Class Padding
 * @package Welhott\Vatlidator\Cleaner
 */
class Padding implements CleanerInterface
{
    /**
     * @var int
     */
    private $length;

    /**
     * @var string
     */
    private $char;

    /**
     * @var int
     */
    private $type;

    /**
     * Padding constructor.
     * @param int $length
     * @param string $char
     * @param int $type
     */
    public function __construct(int $length, string $char, int $type)
    {
        $this->length = $length;
        $this->char = $char;
        $this->type = $type;
    }

    /**
     * @param string $value
     * @return string
     */
    public function transform(string $value): string
    {
        return str_pad($value, $this->length, $this->char, $this->type);
    }
}