<?php
namespace WScore\Validation\Filter;

use WScore\Validation\Rules;
use WScore\Validation\Utils\Filter;
use WScore\Validation\Utils\Helper;
use WScore\Validation\Utils\ValueTO;

class Sanitizers
{
    /**
     * removes null from text.
     *
     * @param ValueTO $v
     */
    public function filter_noNull($v)
    {
        $v->setValue(str_replace("\0", '', $v->getValue()));
    }

    /**
     * trims text.
     *
     * @param ValueTO $v
     */
    public function filter_trim($v)
    {
        $v->setValue(trim($v->getValue()));
    }

    /**
     * options for sanitize.
     *
     * @var array
     */
    public $sanitizes = array(
        'mail'   => FILTER_SANITIZE_EMAIL,
        'float'  => FILTER_SANITIZE_NUMBER_FLOAT,
        'int'    => FILTER_SANITIZE_NUMBER_INT,
        'url'    => FILTER_SANITIZE_URL,
        'string' => FILTER_SANITIZE_STRING,
    );

    /**
     * sanitize the value using filter_var.
     *
     * @param ValueTO $v
     * @param         $p
     */
    public function filter_sanitize($v, $p)
    {
        $option = Helper::arrGet($this->sanitizes, $p, $p);
        $v->setValue(filter_var($v->getValue(), $option));
        if ($p === 'int') {
            if ((int)$v->getValue() !== (int)(float)$v->getValue()) {
                $v->setValue('');
            }
        }
    }

    /**
     * check for valid date-time input string.
     *
     * @param ValueTO $v
     * @param bool|string $p
     */
    public function filter_datetime($v, $p)
    {
        if (is_bool($p) && $p) {
            $p = 'Y-m-d H:i:s';
        }
        $dt = \date_create_from_format($p, $v->getValue());
        if (!$dt) {
            $v->setValue(null);
            $v->setError(__METHOD__, $p);
            return;
        }
        $v->setValue($dt->format($p));
    }

    /**
     * @param ValueTO $v
     * @param null    $p
     */
    public function filter_encoding($v, $p = null)
    {
        $code = (empty($p) || $p === true) ? Filter::$charCode : $p;
        if (!mb_check_encoding($v->getValue(), $code)) {
            $v->setValue(''); // overwrite invalid encode string.
            $v->setError(__METHOD__, $p);
        }
    }

    public $mvConvert = array(
        Rules::MB_HANKAKU  => 'aKVs',
        Rules::MB_ZENKAKU  => 'AKVS',
        Rules::MB_HAN_KANA => 'khs',
        Rules::MB_HIRAGANA => 'HVcS',
        Rules::MB_KATAKANA => 'KVCS',
    );

    /**
     * @param ValueTO $v
     * @param null    $p
     */
    public function filter_mbConvert($v, $p)
    {
        $convert = Helper::arrGet($this->mvConvert, $p, 'KV');
        $v->setValue(mb_convert_kana($v->getValue(), $convert, Filter::$charCode));
    }

    public $stringFilters = [
        Rules::STRING_LOWER => 'strtolower',
        Rules::STRING_UPPER => 'strtoupper',
        Rules::STRING_CAPITAL => 'ucwords',
    ];

    /**
     * @param ValueTO $v
     * @param null    $p
     */
    public function filter_string($v, $p)
    {
        $val = $v->getValue();
        if (!isset($this->stringFilters[$p])) {
            throw new \InvalidArgumentException();
        }
        $func = $this->stringFilters[$p];
        $val = $func($val);
        $v->setValue($val);
    }

    /**
     * if the value is empty (false, null, empty string, or empty array),
     * the default value of $p is used for the value.
     *
     * @param ValueTO $v
     * @param         $p
     */
    public function filter_default($v, $p)
    {
        $val = $v->getValue();
        if (!$val && "" == "{$val}") { // no value. set default...
            $v->setValue($p);
        }
    }
}