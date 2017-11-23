<?php
namespace WScore\Validation\Utils;

use WScore\Validation\Rules;

class Helper
{
    /**
     * @param array|Rules $arr
     * @param string      $key
     * @param null|mixed  $default
     * @return string|array
     */
    public static function arrGet($arr, $key, $default = null)
    {
        if (is_array($arr) || $arr instanceof \ArrayAccess) {
            return isset($arr[$key]) ? $arr[$key] : $default;
        }

        return $default;
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
            list($name, $arg) = self::get_name_and_arg($rule);
            $filter_array[$name] = $arg;
        }

        return $filter_array;
    }

    /**
     * @param string $rule
     * @return array
     */
    private static function get_name_and_arg($rule)
    {
        if (strpos($rule, ':') === false) {
            return [trim($rule), true];
        }
        list($name, $arg) = explode(':', $rule, 2);
        $name = trim($name);
        $arg  = trim($arg);
        $arg  = $arg === 'FALSE' ? false: $arg;

        return [$name, $arg];
    }
}