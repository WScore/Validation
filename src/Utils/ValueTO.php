<?php
namespace WScore\Validation\Utils;

/**
 * value transfer object.
 * Class ValueTO
 *
 * @package WScore\Validation
 */
class ValueTO implements ValueToInterface
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var string
     */
    protected $type = 'text';

    /**
     * @var bool|array
     */
    protected $error = false;

    /**
     * @var string|array
     */
    protected $message;

    /**
     * @var bool
     */
    protected $break = false;

    /**
     * @var null|Message
     */
    protected $messenger;

    /**
     * @param null|Message $messenger
     */
    public function __construct($messenger)
    {
        $this->messenger = $messenger;
    }

    /**
     * @param string|mixed $value
     * @param string $message
     * @return ValueTO
     */
    public static function newValue($value, $message = null)
    {
        $self = new self(null);
        $self->reset($value);
        if ($message) {
            $self->message = $message;
            $self->setError(null);
        }
        
        return $self;
    }    

    /**
     * @param $value
     * @return static
     */
    public function forge($value)
    {
        $obj = clone($this);
        $obj->reset($value);

        return $obj;
    }

    /**
     * @return bool
     */
    public function isValue()
    {
        return true;
    }
    
    /**
     * @param $value
     * @return $this
     */
    public function reset($value)
    {
        $this->value   = $value;
        $this->type    = 'text';
        $this->error   = false;
        $this->message = null;
        $this->break   = false;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return bool|mixed
     */
    public function getValidValue()
    {
        return $this->fails() 
            ? false 
            : $this->getValue();
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return bool
     */
    public function fails()
    {
        return $this->error ? true : false;
    }

    /**
     * @param string     $method
     * @param null|mixed $p
     */
    public function setError($method, $p = null)
    {
        $this->setBreak(true);
        if ($method === false) { // reset error to false.
            $this->error = false;

            return;
        }
        $this->error = array(
            'method'    => $method,
            'parameter' => $p,
            $method     => $p,
        );
    }

    /**
     * @return null|mixed
     */
    public function getParameter()
    {
        if ($this->error && isset($this->error['parameter'])) {
            return $this->error['parameter'];
        }

        return null;
    }

    /**
     * @return string
     */
    public function getErrorMethod()
    {
        if ($this->error && isset($this->error['method'])) {
            return $this->error['method'];
        }

        return null;
    }

    /**
     * gets message regardless of the error state of this ValueTO.
     * use this message ONLY WHEN valueTO is error.
     *
     * @return string|array
     */
    public function message()
    {
        if (!$this->error) {
            return '';
        }
        if (isset($this->message)) {
            return $this->message;
        }
        if ($this->messenger) {
            $type          = $this->getType();
            $method        = $this->getErrorMethod();
            $parameter     = $this->getParameter();
            $this->message = $this->messenger->find($type, $method, $parameter);
            
            return $this->message;
        }

        throw new \BadMethodCallException('cannot return a message. ');
    }

    /**
     * @param string|array $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return boolean
     */
    public function getBreak()
    {
        return $this->break;
    }

    /**
     * @param boolean $break
     */
    public function setBreak($break = true)
    {
        $this->break = $break;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function  __toString()
    {
        return (string)$this->value;
    }
}