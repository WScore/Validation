<?php
namespace WScore\Validation;

use WScore\Validation\Utils\ValueArray;
use WScore\Validation\Utils\ValueTO;
use WScore\Validation\Utils\ValueToInterface;
use WScore\Validation\Utils\Filter;

/**
 * Class Validate
 *
 * @package WScore\Validation
 */
class Verify
{
    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var ValueTO
     */
    private $valueTO;

    // +----------------------------------------------------------------------+
    //  construction
    // +----------------------------------------------------------------------+
    /**
     * @param Filter  $filter
     * @param ValueTO $valueTO
     */
    public function __construct($filter = null, $valueTO = null)
    {
        if (isset($filter)) {
            $this->filter = $filter;
        }
        if (isset($valueTO)) {
            $this->valueTO = $valueTO;
        }
    }

    // +----------------------------------------------------------------------+
    //  validation: for a single value.
    // +----------------------------------------------------------------------+
    /**
     * validates a text value, or an array of text values.
     * returns the filtered value, or false if validation fails.
     *
     * @param string|array $value
     * @param array|Rules $rules
     * @return bool|string
     */
    public function is($value, $rules)
    {
        $valTO = $this->apply($value, $rules);
        if ($valTO->fails()) {
            return false;
        }

        return $valTO->getValue();
    }

    /**
     * @param string|array $value
     * @param array|Rules  $rules
     * @return ValueToInterface
     */
    public function apply($value, $rules)
    {
        // -------------------------------
        // validating a single value.
        if (!is_array($value)) {
            return $this->applyFilters($value, $rules);
        }
        // -------------------------------
        // validating for an array input.
        $values = new ValueArray();
        foreach ($value as $key => $val) {
            $valTO        = $this->apply($val, $rules);
            $values->setValue($key, $valTO);
        }

        return $values;
    }

    /**
     * apply filters on a single value.
     *
     * @param string      $value
     * @param array|Rules $rules
     * @return ValueTo
     */
    public function applyFilters($value, $rules = array())
    {
        /** @var $filter Filter */
        $valueTO = $this->valueTO->forge($value);
        // loop through all the rules to validate $value.
        foreach ($rules as $rule => $parameter) {
            // skip rules with option as FALSE.
            if ($parameter === false) {
                continue;
            }
            $this->applyFilterMethod($rule, $valueTO, $parameter);

            // loop break.
            if ($valueTO->getBreak()) {
                break;
            }
        }

        return $valueTO;
    }

    /**
     * @param string $rule
     * @param ValueTo $valueTO
     * @param mixed $parameter
     */
    private function applyFilterMethod($rule, $valueTO, $parameter)
    {
        $method = 'filter_' . $rule;
        if (method_exists($this->filter, $method)) {
            $this->filter->$method($valueTO, $parameter);
        } elseif (is_callable($parameter)) {
            $this->filter->applyClosure($valueTO, $parameter);
        }
    }
    // +----------------------------------------------------------------------+
}