<?php
namespace WScore\Validation\Utils;

use WScore\Validation\Dio;
use WScore\Validation\Rules;

class HelperRequiredIf
{
    /**
     * @param Dio         $dio
     * @param array|Rules $rules
     * @return array|Rules
     */
    public static function prepare($dio, $rules)
    {
        if (!Helper::arrGet($rules, 'requiredIf')) {
            return $rules;
        }
        list($flag_name, $flags_in) = self::get_flags($rules);
        $value = $dio->get($flag_name);
        $rules = self::set_required_rule($rules, $value, $flags_in);

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
     * @param array|Rules $rules
     * @param string      $value
     * @param array       $flags_in
     * @return array|Rules
     */
    private static function set_required_rule($rules, $value, $flags_in)
    {
        if ("{$value}" !== '') {
            if (empty($flags_in) || in_array($value, $flags_in)) {
                $rules['required'] = true;
            }
        }

        return $rules;
    }
}