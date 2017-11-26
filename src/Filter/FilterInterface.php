<?php
namespace WScore\Validation\Filter;

use WScore\Validation\Utils\ValueTO;

interface FilterInterface
{

    /**
     * @param string  $rule
     * @param ValueTO $valueTO
     * @param mixed   $parameter
     * @return bool
     */
    public function apply($rule, ValueTO $valueTO, $parameter);
}