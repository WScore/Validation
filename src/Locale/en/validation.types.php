<?php

use WScore\Validation\Rules;

// filters for various types of input.

return array(
    'binary'   => [ 'noNull' => false, 'encoding' => false, 'mbConvert' => false, 'trim' => false ],
    'text'     => [],
    'mail'     => [ 'string' => Rules::STRING_LOWER, 'matches' => Rules::MATCH_MAIL, 'sanitize' => 'mail' ],
    'number'   => [ 'matches' => Rules::MATCH_NUMBER ],
    'integer'  => [ 'sanitize'  => 'int', 'matches' => Rules::MATCH_INTEGER, 'max' => PHP_INT_MAX ],
    'float'    => [ 'matches' => Rules::MATCH_FLOAT ],
    'date'     => [ 'datetime' => 'Y-m-d' ],
    'datetime' => [ 'datetime' => 'Y-m-d H:i:s'],
    'month'    => [ 'datetime' => 'Y-m' ],
    'tel'      => [ 'pattern' => '[-0-9()]*' ],
    
    'dateYMD'  => [ 'multiple' => 'YMD', 'datetime' => 'Y-m-d' ],
    'dateYM'   => [ 'multiple' => 'YM',  'datetime' => 'Y-m' ],
    'dateHis'  => [ 'multiple' => 'datetime', 'datetime' => 'Y-m-d H:i:s' ],
    'timeHis'  => [ 'multiple' => 'His', 'datetime' => 'H:i:s' ],
    'timeHi'   => [ 'multiple' => 'Hi',  'datetime' => 'H:i' ],
);