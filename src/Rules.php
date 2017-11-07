<?php
namespace WScore\Validation;

/**
 * about pattern and matches filter.
 * both filters uses preg_match for patter match.
 * it's just pattern is uses in html5's form element, while matches are not.
 *
 */
use Traversable;

/**
 * @method Rules err_msg(string $error_message)
 * @method Rules message(string $message)     set error message.
 * @method Rules multiple(array $parameter)   set multiple field inputs, such as Y, m, and d. 
 * @method Rules array()                      allows array input. ignores array input if not set.
 * @method Rules noNull(bool $not = true)     removes null character. 
 * @method Rules encoding(string $encoding)   validates on character encoding (default is UTF-8). 
 * @method Rules mbConvert(string $type)      converts kana-set (in Japanese). 
 * @method Rules trim(bool $trim = true)      trims input string. 
 * @method Rules sanitize(string $type)       sanitize value. 
 * @method Rules string(string $type)         converts string to upper/lower/etc. 
 * @method Rules custom(\Closure $filter)
 * @method Rules custom2(\Closure $filter)
 * @method Rules custom3(\Closure $filter)
 * @method Rules default(string $value)       sets default value if not set. 
 * @method Rules required(bool $required = true)          required value
 * @method Rules requiredIf(string $key, array $in=[])    set required if $key exists (or in $in). 
 * @method Rules loopBreak(bool $break = true)            breaks filter loop. 
 * @method Rules code(string $type)           
 * @method Rules maxLength(int $length)           maximum character length. 
 * @method Rules pattern(string $reg_expression)  preg_match pattern
 * @method Rules matches(string $match_type)      preset regex patterns (number, int, float, code, mail).
 * @method Rules kanaType(string $match_type)     checks kana-type ().
 * @method Rules min(int $min)                    minimum numeric value.
 * @method Rules max(int $max)                    maximum numeric value.
 * @method Rules range(array $range)              range of [min, max].
 * @method Rules datetime(bool $format = true)    checks for datetime with format. 
 * @method Rules in(array $choices)               checks for list of possible values. 
 * @method Rules confirm(string $key)             same as sameWith
 * @method Rules sameWith(string $key)            confirm against another $key. 
 * @method Rules sameAs(string $value)            compare against $value.
 * @method Rules sameEmpty(bool $check = true)
 *
 * for simplified rules
 * 
 * @method Rules strToLower()
 * @method Rules strToUpper()
 * @method Rules strToCapital()
 * @method Rules mbToHankaku()
 * @method Rules mbToZenkaku()
 * @method Rules mbToHankakuKatakana()
 * @method Rules mbToHiragana()
 * @method Rules mbToKatakana()
 * @method Rules mbOnlyKatakana()
 * @method Rules mbOnlyHiragana()
 * @method Rules mbOnlyHankaku()
 * @method Rules mbOnlyHankakuKatakana()
 * @method Rules sanitizeMail()
 * @method Rules sanitizeFloat()
 * @method Rules sanitizeInt()
 * @method Rules sanitizeUrl()
 * @method Rules sanitizeString()
 * @method Rules matchNumber()
 * @method Rules matchInteger()
 * @method Rules matchFloat()
 * @method Rules matchCode()
 * @method Rules matchMail()
 */
class Rules implements \ArrayAccess, \IteratorAggregate
{
    /*
     * convert string to... 
     */
    const STRING_LOWER = 'lower';
    const STRING_UPPER = 'upper';
    const STRING_CAPITAL = 'capital';
    
    /*
     * converts mb character to...
     */
    const MB_HANKAKU = 'han_kaku';
    const MB_ZENKAKU = 'zen_kaku';
    const MB_HAN_KANA = 'han_kana';
    const MB_HIRAGANA = 'hiragana';
    const MB_KATAKANA = 'katakana';

    /*
     * validates the mb character type.
     */
    const ONLY_KATAKANA = 'katakana';
    const ONLY_HIRAGANA = 'hiragana';
    const ONLY_HANKAKU = 'hankaku';
    const ONLY_HANKAKU_KANA = 'hankana';

    /**
     * this is the mother of $filter.
     *
     * @var array
     */
    protected $baseFilters = array();

    /**
     * @var array        predefined filter filter set
     */
    protected $filterTypes = array();

    /**
     * @var array
     */
    protected $filter = array();

    /**
     * @var string
     */
    private $type;

    // +----------------------------------------------------------------------+
    //  managing object
    // +----------------------------------------------------------------------+
    /**
     * @param null|string $locale
     * @param null|string $dir
     */
    public function __construct($locale = null, $dir = null)
    {
        $locale = $locale ?: 'en';
        $dir    = $dir ?: __DIR__ . '/Locale/';
        $dir   .= $locale . '/';
        
        /** @noinspection PhpIncludeInspection */
        $this->baseFilters = include($dir . "validation.filters.php");
        $this->filter      = $this->baseFilters;
        
        /** @noinspection PhpIncludeInspection */
        $types = include($dir . "validation.types.php");
        foreach ($types as $key => $info) {
            $key = strtolower($key);
            $this->filterTypes[$key] = $info;
        }
    }
    
    /**
     * @param $type
     * @return Rules|$this
     */
    public function withType($type)
    {
        $rule = clone($this);
        $rule->applyType($type);

        return $rule;
    }
    // +----------------------------------------------------------------------+
    //  setting rule
    // +----------------------------------------------------------------------+
    /**
     * @param string $type
     * @return Rules|$this
     * @throws \BadMethodCallException
     */
    public function applyType($type)
    {
        $type       = strtolower($type);
        if ($type === 'email') {
            $type = 'mail';
        }
        $this->type = $type;
        if (!array_key_exists($type, $this->filterTypes)) {
            throw new \BadMethodCallException("undefined type: {$type}");
        }
        $this->filter         = array_merge($this->baseFilters, $this->filterTypes[$type]);
        $this->filter['type'] = $type;

        return $this;
    }

    /**
     * @param array|string $filters
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function apply($filters)
    {
        if (is_string($filters)) {
            $filters = Utils\Helper::convertFilter($filters);
        }
        if (!is_array($filters)) {
            throw new \InvalidArgumentException("filters must be an array or a text string. ");
        }
        foreach ($filters as $rule => $parameter) {
            if (is_numeric($rule)) {
                $rule      = $parameter;
                $parameter = true;
            }
            $this->filter[$rule] = $parameter;
        }

        return $this;
    }

    public $convertRules = [
        'confirm' => 'sameWith',
        'err_msg' => 'message',
    ];
    
    private $simples = [
        'strToLower' => ['string', self::STRING_LOWER],
        'strToUpper' => ['string', self::STRING_UPPER],
        'strToCapital' => ['string', self::STRING_CAPITAL],
        
        'mbToHankaku' => ['mbConvert', self::MB_HANKAKU],
        'mbToZenkaku' => ['mbConvert', self::MB_ZENKAKU],
        'mbToHankakuKatakana' => ['mbConvert', self::MB_HAN_KANA],
        'mbToHiragana' => ['mbConvert', self::MB_HIRAGANA],
        'mbToKatakana' => ['mbConvert', self::MB_KATAKANA],
        
        'sanitizeMail' => ['sanitize', 'mail'],
        'sanitizeFloat' => ['sanitize', 'float'],
        'sanitizeInt' => ['sanitize', 'int'],
        'sanitizeUrl' => ['sanitize', 'url'],
        'sanitizeString' => ['sanitize', 'string'],
        
        'matchNumber' => ['match', 'number'],
        'matchInteger' => ['match', 'int'],
        'matchFloat' => ['match', 'float'],
        'matchCode' => ['match', 'code'],
        'matchMail' => ['match', 'mail'],
    ];
    
    /**
     * @param $rule
     * @param $args
     * @return $this
     */
    public function __call($rule, $args)
    {
        if (isset($this->simples[$rule])) {
            $args = [$this->simples[$rule][1]];
            $rule = $this->simples[$rule][0];
        }
        if(empty($args)) {
            $value = true;
        } elseif(count($args) === 1) {
            $value = $args[0];
        } else {
            $value = $args;
        }
        $rule = array_key_exists($rule, $this->convertRules) ? $this->convertRules[$rule] : $rule;
        $this->filter[$rule] = $value;

        return $this;
    }
    // +----------------------------------------------------------------------+
    //  getting information about Rule
    // +----------------------------------------------------------------------+
    /**
     * adds the custom filter to the rules.
     * the name, 'custom', 'custom2', and 'custom3', are reserved
     * for the filters (before the validation).
     *
     * @param string   $name
     * @param \Closure $filter
     * @return Rules
     */
    public function addCustom($name, $filter)
    {
        $this->filter[$name] = $filter;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getType()
    {
        return $this->filter['type'];
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return !!$this->filter['required'];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->filter;
    }
    // +----------------------------------------------------------------------+
    //  for ArrayAccess and IteratorAggregate.
    // +----------------------------------------------------------------------+
    /**
     * Whether a offset exists
     *
     * @param mixed $offset An offset to check for.
     * @return boolean true on success or false on failure.
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->filter);
    }

    /**
     * Offset to retrieve
     *
     * @param mixed $offset The offset to retrieve.
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return array_key_exists($offset, $this->filter) ? $this->filter[$offset] : null;
    }

    /**
     * Offset to set
     *
     * @param mixed $offset The offset to assign the value to.
     * @param mixed $value  The value to set.
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->filter[$offset] = $value;
    }

    /**
     * Offset to unset
     *
     * @param mixed $offset The offset to unset.
     * @return void
     */
    public function offsetUnset($offset)
    {
        if (array_key_exists($offset, $this->filter)) {
            unset($this->filter[$offset]);
        }
    }

    /**
     * Retrieve an external iterator
     *
     * @return Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->filter);
    }
}