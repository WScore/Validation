<?php
namespace WScore\Validation;

use WScore\Validation\Utils\Helper;
use WScore\Validation\Utils\ValueArray;
use WScore\Validation\Utils\ValueTO;
use WScore\Validation\Utils\ValueToInterface;

/**
 * Class Dio
 *
 * @package WScore\Validation
 *
 * Data Import Object
 * for validating a a data, an array of values (i.e. input from html form).
 */
class Dio
{
    /**
     * @var array                 source of data to read from
     */
    private $source = array();

    /**
     * @var Rules[]
     */
    private $rules = [];

    /**
     * @var bool
     */
    private $isEvaluated = false;

    /**
     * @var ValueArray                 validated and invalidated data
     */
    private $found = array();

    /**
     * @var int                   number of errors (invalids)
     */
    private $err_num = 0;

    /**
     * @var Verify
     */
    public $verify = null;

    /**
     * @var Rules
     */
    private $ruler;

    // +----------------------------------------------------------------------+
    /**
     * @Inject
     * @param Verify $verify
     * @param Rules  $rules
     */
    public function __construct($verify, $rules)
    {
        $this->verify = $verify;
        $this->ruler  = $rules;
    }

    /**
     * @param array $data
     */
    public function source($data = array())
    {
        $this->source = $data;
        $this->isEvaluated = false;
        $this->err_num = 0;
        $this->found = new ValueArray();
    }

    /**
     * @param string $key
     * @return Rules
     */
    public function getRule($key)
    {
        $this->isEvaluated = false;
        $this->found->deleteValue($key);
        if (array_key_exists($key, $this->rules)) {
            return $this->rules[$key];
        }
        return null;
    }

    /**
     * @param string $key
     * @return RuleType
     */
    public function set($key)
    {
        $this->isEvaluated = false;

        $rules = clone $this->ruler;
        $this->rules[$key] = $rules;
        $this->found->deleteValue($key);

        return new RuleType($rules);
    }
    
    /**
     * evaluate all the rules and saves the into $this->found.
     */
    private function evaluateAll()
    {
        if($this->isEvaluated) {
            return;
        }
        foreach($this->rules as $key => $rule) {
            $this->evaluateAndGet($key);
        }
    }
    // +----------------------------------------------------------------------+
    //  getting found values
    // +----------------------------------------------------------------------+
    /**
     * sets rules for the $key, and returns the evaluated value.
     * returns false if invalidated.
     *
     * @param string      $key
     * @return bool|mixed
     */
    public function get($key)
    {
        $valTO = $this->evaluate($key);
        if ($valTO->fails()) {
            return false;
        }

        return $valTO->getValidValue();
    }

    /**
     * evaluate for $key and stores results in $this->found and $this->messages. 
     *
     * @param string $key
     */
    private function evaluateAndGet($key)
    {
        $valTO = $this->evaluate($key);
        $this->setValue($key, $valTO);

        if ($valTO->fails()) {
            $this->err_num++;
        }
    }

    /**
     * returns all the evaluated values including invalidated one.
     *
     * @return array
     */
    public function getAll()
    {
        $this->evaluateAll();

        return $this->found->getValue();
    }

    /**
     * returns all the valid values.
     *
     * @return array
     */
    public function getSafe()
    {
        $this->evaluateAll();

        return $this->found->getValidValue();
    }

    /**
     * @param string $key
     * @param mixed  $value
     * @return Dio
     */
    public function setValue($key, $value)
    {
        if (!$value instanceof ValueToInterface) {
            $value = ValueTO::newValue($value);
        }
        $this->found->setValue($key, $value);

        return $this;
    }
    
    // +----------------------------------------------------------------------+
    //  errors and messages
    // +----------------------------------------------------------------------+
    /**
     * @return bool
     */
    public function fails()
    {
        $this->evaluateAll();
        return $this->err_num ? true : false;
    }

    /**
     * @return bool
     */
    public function passes()
    {
        $this->evaluateAll();
        return $this->err_num ? false : true;
    }

    /**
     * @param null|string $key
     * @return array|mixed
     */
    public function getMessages($key = null)
    {
        $this->evaluateAll();
        if (!is_null($key)) {
            $value = $this->found->getValueTo($key);
            return $value ? $value->message() : '';
        }
        
        return $this->found->message();
    }

    /**
     * @param string     $key
     * @param mixed      $error
     * @param bool|mixed $value
     * @return Dio
     */
    public function setError($key, $error, $value = false)
    {
        $this->found->setValue($key, ValueTO::newValue($value, $error));
        $this->err_num++;

        return $this;
    }

    // +----------------------------------------------------------------------+
    //  find and validate and save it to found
    // +----------------------------------------------------------------------+
    /**
     * finds a value with $key in the source data.
     *
     * @param string      $key
     * @param array|Rules $rules
     * @return string|array
     */
    public function find($key, $rules = [])
    {
        if (Utils\Helper::arrGet($rules, 'multiple')) {
            // check for multiple case i.e. Y-m-d.
            return Utils\HelperMultiple::prepare($key, $this->source, $rules['multiple']);
        }
        $value = Helper::arrGet($this->source, $key);
        if (is_array($value)) {
            return Utils\Helper::arrGet($rules, 'array')
                ? $value
                : '';
        }

        return (string) $value;
    }

    /**
     * set up rules; 
     * - add required rule based on requiredIf rule.
     * - add sameAs rule based on sameWith rule. 
     * 
     * @param array|Rules $rules
     * @return array|Rules
     */
    private function setupRules($rules)
    {
        // prepares filter for requiredIf
        $rules = Utils\HelperRequiredIf::prepare($this, $rules);

        // prepares filter for sameWith.
        $rules = Utils\HelperSameWith::prepare($this, $rules);

        return $rules;
    }

    /**
     * @param $key
     * @return Utils\ValueToInterface
     */
    private function evaluate($key)
    {
        $rules = array_key_exists($key, $this->rules) ? $this->rules[$key] : $this->ruler->withType('text');
        $rules = $this->setupRules($rules);
        $found = $this->find($key, $rules);
        $valTO = $this->verify->apply($found, $rules);
        return $valTO;
    }
    // +----------------------------------------------------------------------+
}