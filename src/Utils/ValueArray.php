<?php
namespace WScore\Validation\Utils;

class ValueArray implements ValueToInterface
{
    /**
     * @var ValueToInterface[]
     */
    private $values = [];

    /**
     * @var bool
     */
    protected $error = false;

    /**
     * @return bool
     */
    public function isValue()
    {
        return false;
    }

    /**
     * @param string $key
     * @param ValueToInterface $value
     */
    public function setValue($key, $value)
    {
        $this->values[$key] = $value;
        if ($value->fails()) {
            $this->error = true;
        }
    }

    /**
     * @param string $key
     */
    public function deleteValue($key)
    {
        unset($this->values[$key]);
    }
    
    /**
     * returns the value.
     * the value maybe invalid.
     *
     * @return mixed
     */
    public function getValue()
    {
        $values = [];
        foreach($this->values as $key => $value) {
            $values[$key] = $value->getValue();
        }
        return $values;
    }

    /**
     * @param string $key
     * @return null|ValueToInterface
     */
    public function getValueTo($key)
    {
        return array_key_exists($key, $this->values)
            ? $this->values[$key]
            : null;
    }

    /**
     * return the validated value.
     * returns false if validation fails.
     *
     * @return bool|mixed
     */
    public function getValidValue()
    {
        $values = [];
        foreach($this->values as $key => $value) {
            if ($this->isValidValue($value)) {
                $values[$key] = $value->getValidValue();
            }
        }
        return $values;
    }
    
    private function isValidValue(ValueToInterface $value)
    {
        return !$value->isValue() || !$value->fails();
    }

    /**
     * returns validation type, such as text, mail, date, etc.
     *
     * @return string
     */
    public function getType()
    {
        if ($value = reset($this->values)) {
            return $value->getType();
        }
        
        return '';
    }

    /**
     * gets message regardless of the error state of this ValueTO.
     * use this message ONLY WHEN valueTO is error.
     *
     * @return string|array
     */
    public function message()
    {
        $messages = [];
        foreach($this->values as $key => $value) {
            if ($value->fails()) {
                $messages[$key] = $value->message();
            }
        }
        return $messages;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        $values = $this->getValue();
        return implode(',', $values);
    }

    /**
     * @return bool
     */
    public function fails()
    {
        return $this->error;
    }
}