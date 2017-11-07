<?php
namespace WScore\Validation;

/**
 * Class RuleType
 *
 * for HTML/HTML5 input types:
 * 
 * @method Rules asText()
 * @method Rules asMail()
 * @method Rules asBinary()
 * @method Rules asNumber()
 * @method Rules asInteger()
 * @method Rules asFloat()
 * @method Rules asDate()
 * @method Rules asDatetime()
 * @method Rules asMonth()
 * 
 * for multiple type:
 * 
 * @method Rules asDateYMD()
 * @method Rules asDateYM()
 * @method Rules asDateHis()
 * @method Rules asTime()
 * @method Rules asTimeHi()
 * @method Rules asTel()
 * 
 * for multi-byte (Japanese) input:
 * 
 * @method Rules asHiragana()
 * @method Rules asKatakana()
 */
class RuleType
{
    /**
     * @var Rules
     */
    private $rules;

    /**
     * RuleType constructor.
     *
     * @param Rules $rules
     */
    public function __construct($rules)
    {
        $this->rules = $rules;
    }

    /**
     * @param string $name
     * @param array $args
     * @return Rules
     */
    public function __call($name, $args)
    {
        if (substr($name, 0, 2) !== 'as') {
            throw new \BadMethodCallException;
        }
        $type = substr($name, 2);
        $this->rules->applyType($type);
        return $this->rules;
    }
}