<?php
namespace WScore\Validation\Filter;

use WScore\Validation\Rules;
use WScore\Validation\Utils\Helper;
use WScore\Validation\Utils\ValueTO;

class Verifiers
{
    /**
     * checks if the $value has some value.
     *
     * @param ValueTO $v
     */
    public function filter_required($v)
    {
        $val = $v->getValue();
        if ($val) {
            return;
        }
        if ("{$val}" === '') {
            // the value is empty. check if it is "required".
            $v->setError(__METHOD__);
        }
    }

    // +----------------------------------------------------------------------+
    //  filter definitions (filters for validation).
    // +----------------------------------------------------------------------+
    /**
     * breaks loop if value is empty by returning $loop='break'.
     * validation is not necessary for empty value.
     *
     * @param ValueTO $v
     */
    public function filter_loopBreak($v)
    {
        $val = $v->getValue();
        if ("{$val}" == '') { // value is really empty. break the loop.
            $v->setBreak(true); // skip subsequent validations for empty values.
        }
    }

    /**
     * options for patterns.
     *
     * @var array
     */
    public $matchType = array(
        Rules::MATCH_NUMBER  => '[0-9]+',
        Rules::MATCH_INTEGER => '[-0-9]+',
        Rules::MATCH_FLOAT   => '[-.0-9]+',
        Rules::MATCH_CODE    => '[-_0-9a-zA-Z]+',
        Rules::MATCH_MAIL    => '[a-zA-Z0-9_.-]+@[a-zA-Z0-9_.-]+\.[a-zA-Z]+',
    );

    /**
     * @param ValueTO $v
     * @param         $p
     */
    public function filter_matches($v, $p)
    {
        $matchType = Helper::arrGet($this->matchType, $p, $p);
        $this->pattern($v, $matchType, __METHOD__, $p);
    }

    /**
     * @param ValueTO $v
     * @param         $p
     */
    public function filter_kanaType($v, $p)
    {
        $kanaType = Helper::arrGet($this->kanaType, $p, $p);
        $this->pattern($v, $kanaType, __METHOD__, $p);
    }

    /**
     * @var array
     */
    public $kanaType = array(
        Rules::ONLY_KATAKANA => '[　ーァ-ヶ・ーヽヾ1-2１-２]*',
        Rules::ONLY_HIRAGANA => '[　ぁ-ん゛-ゞ1-2１-２]+',
        Rules::ONLY_HANKAKU_KANA  => '[ ｦ-ﾝﾞﾟ1-2]+',
        Rules::ONLY_HANKAKU  => '[ -~]+',
    );

    /**
     * @param ValueTO $v
     * @param         $p
     */
    public function filter_pattern($v, $p)
    {
        $this->pattern($v, $p, __METHOD__, $p);
    }

    private function pattern(ValueTo $v, $pattern, $method, $parameter)
    {
        if (!preg_match("/^{$pattern}$/", $v->getValue())) {
            $v->setError($method, $parameter);
        }
    }

    /**
     * @param ValueTO $v
     * @param         $p
     */
    public function filter_in($v, $p)
    {
        if (!is_array($p)) {
            $p = array($p);
        }
        if (!in_array($v->getValue(), $p)) {
            $v->setError(__METHOD__, $p);
        }
    }

    /**
     * @param ValueTO $v
     * @param         $p
     */
    public function filter_sameAs($v, $p)
    {
        if ($v->getValue() !== $p) {
            $v->setError(__METHOD__, $p);
        }
    }

    /**
     * @param ValueTO $v
     */
    public function filter_sameEmpty($v)
    {
        $val = $v->getValue();
        if ("{$val}" !== "") {
            $v->setError(__METHOD__);
        }
    }

    /**
     * @param ValueTO $v
     * @param         $p
     */
    public function filter_max($v, $p)
    {
        $val = (int)$v->getValue();
        if ($val > (int)$p) {
            $v->setError(__METHOD__);
        }
    }

    /**
     * @param ValueTO $v
     * @param         $p
     */
    public function filter_min($v, $p)
    {
        $val = (int)$v->getValue();
        if ($val < (int)$p) {
            $v->setError(__METHOD__);
        }
    }

    /**
     * @param ValueTO    $v
     * @param string|int $p
     */
    public function filter_maxLength($v, $p)
    {
        if (mb_strlen($v) > $p) {
            $v->setError(__METHOD__, $p);
        }
    }
}