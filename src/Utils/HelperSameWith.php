<?php
namespace WScore\Validation\Utils;

use WScore\Validation\Dio;
use WScore\Validation\Rules;

class HelperSameWith
{
    // +----------------------------------------------------------------------+
    /**
     * prepares filter for sameWith rule.
     * get another value to compare in sameWith, and compare it with the value using sameAs rule.
     *
     * @param Dio         $dio
     * @param array|Rules $rules
     * @return array|Rules
     */
    public static function prepare($dio, $rules)
    {
        if (!Helper::arrGet($rules, 'sameWith')) {
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