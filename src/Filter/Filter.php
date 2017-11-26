<?php
namespace WScore\Validation\Filter;

use WScore\Validation\Utils\ValueTO;

class Filter extends AbstractFilter
{
    public static $charCode = 'UTF-8';

    /**
     * @var FilterInterface[]
     */
    private $filters = [];

    /**
     * @param FilterInterface[] $filters
     */
    public function __construct($filters = [])
    {
        if ($filters) {
            $this->filters = $filters;
        } else {
            $this->filters[] = new Sanitizers();
            $this->filters[] = new Verifiers();
        }
    }

    /**
     * @param string  $rule
     * @param ValueTO $valueTO
     * @param mixed   $parameter
     * @return bool
     */
    public function apply($rule, ValueTO $valueTO, $parameter)
    {
        $method = 'filter_' . $rule;
        if (!is_string($parameter) && is_callable($parameter)) {
            $parameter($valueTO, $parameter);
            return true;
        }
        if (method_exists($this, $method)) {
            $this->$method($valueTO, $parameter);
            return true;
        }
        foreach($this->filters as $filter) {
            if ($filter->apply($rule, $valueTO, $parameter)) {
                return true;
            }
        }
        return false;
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
