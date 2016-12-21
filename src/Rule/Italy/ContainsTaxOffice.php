<?php
/**
 * Created by PhpStorm.
 * User: rvelhote
 * Date: 12/21/16
 * Time: 11:08 PM
 */

namespace Welhott\Vatlidator\Rule\Italy;


use Welhott\Vatlidator\Rule\RuleInterface;

class ContainsTaxOffice implements RuleInterface
{
    /**
     * The list of valid tax offices. Required for validation because they must belong the number to validate.
     * This list is generated in the constructor dynamically.
     * @var array
     */
    private $taxOffices = [];

    /**
     * ContainsTaxOffice constructor.
     */
    public function __construct()
    {
        // Create the rest of the tax offices automatically.
        $callback = function(int $code) {
            return str_pad($code, 3, 0, STR_PAD_LEFT);
        };

        // TODO Maybe this is not cool for performance or memory usage (if many numbers are being validated?)
        $this->taxOffices = array_map($callback, range(1, 100));

        // FIXME Why not an array_merge?
        array_push($this->taxOffices, '120');
        array_push($this->taxOffices, '121');
        array_push($this->taxOffices, '888');
        array_push($this->taxOffices, '999');
    }

    /**
     * @param string $value
     * @return bool
     */
    public function validate(string $value): bool
    {
        $taxOffice = mb_substr($value, 7, 3);
        return in_array($taxOffice, $this->taxOffices, true);
    }

    /**
     * Obtain the error configured by a rule.
     * @return string|null The error that this rule returns when the validation fails.
     */
    public function getError(): string
    {
        return 'The number does not contain a valid Tax Office.';
    }
}