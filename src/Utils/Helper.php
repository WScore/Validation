<?php
namespace WScore\Validation\Utils;

use WScore\Validation\Dio;
use WScore\Validation\Rules;

class Helper
{
    /**
     * @param array|Rules $arr
     * @param string      $key
     * @param null|mixed  $default
     * @return mixed
     */
    public static function arrGet($arr, $key, $default = null)
    {
        if (!is_string($key)) {
            return $default;
        }
        if (!is_array($arr) && (is_object($arr) && !($arr instanceof \ArrayAccess))) {
            return $default;
        }

        return isset($arr[$key]) ? $arr[$key] : $default;
    }

    /**
     * converts string filter to array. string in: 'rule1:parameter1|rule2:parameter2'
     *
     * @param string|array $filter
     * @return array
     */
    public static function convertFilter($filter)
    {
        if (!$filter) {
            return array();
        }
        if (is_array($filter)) {
            return $filter;
        }

        $filter_array = array();
        $rules        = explode('|', $filter);
        foreach ($rules as $rule) {
            $filter = explode(':', $rule, 2);
            $arg = isset($filter[1]) ? trim($filter[1]) : true;
            $arg = $arg === 'FALSE' ? false: $arg;
            $filter_array[trim($filter[0])] = $arg;
        }

        return $filter_array;
    }
    
    // +----------------------------------------------------------------------+
    /**
     * prepares filter for sameWith rule.
     * get another value to compare in sameWith, and compare it with the value using sameAs rule.
     *
     * @param Dio         $dio
     * @param array|Rules $rules
     * @return array|Rules
     */
    public static function prepare_sameWith($dio, $rules)
    {
        if (!self::arrGet($rules, 'sameWith')) {
            return $rules;
        }
        $value = self::get_sameWith_value($dio, $rules);
        // reset sameWith filter, and set same{As|Empty} filter.
        $rules['sameWith'] = false;
        if ($value) {
            $rules['sameAs'] = $value;
        } else {
            $rules['sameEmpty'] = true;
        }

        return $rules;
    }

    /**
     * @param Dio         $dio
     * @param array|Rules $rules
     * @return array|Rules
     */
    public static function prepare_requiredIf($dio, $rules)
    {
        if (!self::arrGet($rules, 'requiredIf')) {
            return $rules;
        }
        list($flag_name, $flags_in) = self::get_flags($rules);
        $flag_value = $dio->get($flag_name);
        if ((string)$flag_value === '') {
            return $rules;
        }
        if (!empty($flags_in) && !in_array($flag_value, $flags_in)) {
            return $rules;
        }
        $rules['required'] = true;

        return $rules;
    }

    /**
     * @param array|Rules $rules
     * @return array
     */
    private static function get_flags($rules)
    {
        $args = $rules['requiredIf'];
        if (!is_array($args)) {
            $flag_name = $args;
            $flags_in  = [];
        } else {
            $flag_name = $args[0];
            $flags_in  = array_key_exists(1, $args) ? (array)$args[1] : [];
        }

        return array($flag_name, $flags_in);
    }

    /**
     * find the same with value.
     * 
     * @param Dio         $dio
     * @param array|Rules $rules
     * @return mixed
     */
    private static function get_sameWith_value($dio, $rules)
    {
        $sub_name = $rules['sameWith'];
        if ($rules instanceof Rules) {
            $sub_filter = clone $rules;
        } else {
            $sub_filter = $rules;
        }
        $sub_filter['sameWith'] = false;
        $sub_filter['required'] = false;
        $value                  = $dio->find($sub_name, $sub_filter);
        $value                  = $dio->verify->is($value, $sub_filter);

        return $value;
    }
}