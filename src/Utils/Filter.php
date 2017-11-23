<?php
namespace WScore\Validation\Utils;

use WScore\Validation\Filter\Sanitizers;
use WScore\Validation\Filter\Verifiers;

class Filter
{
    public static $charCode = 'UTF-8';

    /**
     * @var array
     */
    private $filters = [];

    // +----------------------------------------------------------------------+
    /**
     */
    public function __construct()
    {
        $this->filters[] = new Sanitizers();
        $this->filters[] = new Verifiers();
    }

    /**
     * @param string  $rule
     * @param ValueTO $valueTO
     * @param mixed   $parameter
     */
    public function apply($rule, ValueTO $valueTO, $parameter)
    {
        $method = 'filter_' . $rule;
        if (method_exists($this, $method)) {
            $this->$method($valueTO, $parameter);
            return;
        }
        foreach($this->filters as $filter) {
            if (method_exists($filter, $method)) {
                $filter->$method($valueTO, $parameter);
                return;
            }
        }
        if (!is_string($parameter) && is_callable($parameter)) {
            $this->applyClosure($valueTO, $parameter);
            return;
        }
    }

    /**
     * @param ValueTO  $v
     * @param \Closure $closure
     */
    public function applyClosure($v, $closure)
    {
        $closure($v);
    }

    // +----------------------------------------------------------------------+
    //  filter definitions (filters that alters the value).
    // +----------------------------------------------------------------------+
    /**
     * @param ValueTO $v
     * @param         $p
     */
    public function filter_type($v, $p)
    {
        $v->setType($p);
    }

    /**
     * sets error message.
     *
     * @param ValueTO $v
     * @param         $p
     */
    public function filter_err_msg($v, $p)
    {
        $this->filter_message($v, $p);
    }

    /**
     * sets error message.
     *
     * @param ValueTO $v
     * @param         $p
     */
    public function filter_message($v, $p)
    {
        if ($p) {
            $v->setMessage($p);
        }
    }


    // +----------------------------------------------------------------------+
}
