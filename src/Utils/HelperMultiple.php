<?php
namespace WScore\Validation\Utils;

class HelperMultiple
{
    // +----------------------------------------------------------------------+
    //  special filters for multiple and sameWith rules.
    // +----------------------------------------------------------------------+
    /**
     * @var array   options for multiple preparation.
     */
    public static $multiples = array(
        'date'     => array('suffix' => 'y,m,d', 'connector' => '-',),
        'YMD'      => array('suffix' => 'y,m,d', 'connector' => '-',),
        'YM'       => array('suffix' => 'y,m', 'connector' => '-',),
        'time'     => array('suffix' => 'h,i,s', 'connector' => ':',),
        'His'      => array('suffix' => 'h,i,s', 'connector' => ':',),
        'Hi'       => array('suffix' => 'h,i', 'connector' => ':',),
        'datetime' => array('suffix' => 'y,m,d,h,i,s', 'format' => '%04d-%02d-%02d %02d:%02d:%02d',),
        'tel'      => array('suffix' => '1,2,3', 'connector' => '-',),
        'credit'   => array('suffix' => '1,2,3,4', 'connector' => '',),
        'amex'     => array('suffix' => '1,2,3', 'connector' => '',),
    );

    /**
     * prepares for validation by creating a value from multiple value.
     *
     * @param string       $name
     * @param array        $source
     * @param string|array $option
     * @return mixed|null|string
     */
    public static function prepare($name, $source, $option)
    {
        // get options.
        if (is_string($option)) {
            $option = (array) Helper::arrGet(self::$multiples, $option, array());
        }
        $lists = self::find_multiple($name, $source, $option);
        $found = self::merge_multiple($option, $lists);


        return $found;
    }
    
    /**
     * find multiples values from suffix list.
     * 
     * @param string $name
     * @param array $source
     * @param array $option
     * @return array
     */
    private static function find_multiple($name, $source, $option)
    {
        $sep    = Helper::arrGet($option, 'separator', '_');
        $suffix = explode(',', $option['suffix']);
        $lists  = [];
        foreach ($suffix as $sfx) {
            $name_sfx = $name . $sep . $sfx;
            if (array_key_exists($name_sfx, $source) && trim($source[$name_sfx])) {
                $lists[] = trim($source[$name_sfx]);
            }
        }

        return $lists;
    }

    /**
     * merge the found list into one value.
     * 
     * @param array $option
     * @param array $lists
     * @return mixed|null|string
     */
    private static function merge_multiple($option, $lists)
    {
        if (empty($lists)) {
            return null;
        }
        // found format using sprintf.
        if (isset($option['format'])) {
            $param = array_merge(array($option['format']), $lists);
            $found = call_user_func_array('sprintf', $param);
        } else {
            $con   = Helper::arrGet($option, 'connector', '-');
            $found = implode($con, $lists);
        }

        return $found;
    }

}