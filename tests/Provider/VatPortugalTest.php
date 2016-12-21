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
namespace Welhott\Vatlidator\Tests\Provider;

use Welhott\Vatlidator\Provider\VatPortugal;
use Welhott\Vatlidator\Tests\BaseVatValidationInterface;
use Welhott\Vatlidator\Tests\BaseVatValidationTest;

/**
 * Class VatPortugalTest
 * @package Welhott\Vatlidator\Provider\Tests
 */
class VatPortugalTest extends BaseVatValidationTest implements BaseVatValidationInterface
{
    /**
     * @var string
     */
    private $country = 'PT';

    /**
     * @var string
     */
    private $countryName = 'Portugal';

    /**
     * @var string
     */
    private $abbreviation = 'NIF';

    /**
     * @test Confirm that the provided VAT number for Portugal is valid.
     * @dataProvider getValidVatNumbers
     * @param string $number The number to validate from the dataProvider
     */
    public function testValidPortugueseVat(string $number)
    {
        $validator = new VatPortugal($number);

        $this->assertTrue($validator->validate());
        $this->assertEquals($this->country, $validator->getCountry());
        $this->assertEquals($number, $validator->getNumber());
        $this->assertEquals($this->abbreviation, $validator->getAbbreviation());
    }

    /**
     * @test Confirm that the provided VAT number for Portugal is invalid.
     * @dataProvider getInvalidVatNumbers
     * @param string $number The number to validate from the dataProvider
     */
    public function testInvalidPortugueseVat(string $number)
    {
        $validator = new VatPortugal($number);

        $this->assertFalse($validator->validate(), sprintf('%s should be invalid', $number));
        $this->assertEquals($this->country, $validator->getCountry());
        $this->assertEquals($number, $validator->getNumber());
    }

    /**
     * Obtain a list of valid VAT numbers.
     * @return array A dataset containing a list of valid numbers to check.
     */
    public function getValidVatNumbers()
    {
        return $this->getValidDataset($this->countryName);
    }

    /**
     * Obtain a list of invalid VAT numbers.
     * @return array A dataset containing a list of invalid numbers to check.
     */
    public function getInvalidVatNumbers()
    {
        return $this->getInvalidDataset($this->countryName);
    }
}
