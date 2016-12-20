<?php
/**
 * Created by PhpStorm.
 * User: rvelhote
 * Date: 12/20/16
 * Time: 9:05 PM
 */

namespace Welhott\Vatlidator\Rule;

/**
 * Interface RuleInterface
 * @package Welhott\Vatlidator\Rule
 */
interface RuleInterface
{
    /**
     * @param string $value
     * @return bool
     */
    public function validate(string $value) : bool;
}
