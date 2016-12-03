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
namespace Welhott\Vatlidator\Tests;

use PHPUnit_Framework_TestCase;

/**
 * Class VatValidatorTest
 * @package Welhott\Vatlidator\Tests
 */
class BaseVatValidationTest extends PHPUnit_Framework_TestCase implements BaseVatValidationInterface
{
    /**
     *
     */
    public function testFilesExist() {
        $countries = [
            'Poland',
            'Portugal',
            'Spain',
        ];

        $filenames = [
            'valid.txt',
            'invalid.txt',
        ];

        foreach($countries as $country) {
            foreach($filenames as $filename) {
                $path = __DIR__.'/Dataset/'.$country.'/'.$filename;
                $this->assertFileExists($path, sprintf('File %s does not exist!', $path));
            }
        }
    }

    /**
     * @param string $country
     * @return array
     */
    public function getValidDataset(string $country) {
        return $this->getDatasetFromFile($country, 'valid');
    }

    /**
     * @param string $country
     * @return array
     */
    public function getInvalidDataset(string $country) {
        return $this->getDatasetFromFile($country, 'invalid');
    }

    /**
     * @param string $country
     * @param string $filename
     * @return array
     */
    private function getDatasetFromFile(string $country, string $filename) {
        $dataset = preg_split('/\r\n|\r|\n/', file_get_contents(__DIR__.'/Dataset/'.$country.'/'.$filename.'.txt'));

        $dataset = array_map(function($number) {
            return [$number];
        }, $dataset);

        return $dataset;
    }
}
