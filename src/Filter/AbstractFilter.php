<?php
namespace WScore\Validation\Filter;

use WScore\Validation\Utils\ValueTO;

class AbstractFilter implements FilterInterface
{
    /**
     * @param string  $rule
     * @param ValueTO $valueTO
     * @param mixed   $parameter
     * @return bool
     */
    public function apply($rule, ValueTO $valueTO, $parameter)
    {
        $method = 'filter_' . $rule;
        if (method_exists($this, $method)) {
            $this->$method($valueTO, $parameter);
            return true;
        }
        if (!is_string($parameter) && is_callable($parameter)) {
            $parameter($valueTO, $parameter);
            return true;
        }
        return false;
    }
}