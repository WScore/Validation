<?php

// filters for various types of input.

return array(
    'binary'   => [ 'noNull' => false, 'encoding' => false, 'mbConvert' => false, 'trim' => false ],
    'text'     => [],
    'mail'     => [ 'string' => 'lower', 'matches' => 'mail', 'sanitize' => 'mail' ],
    'number'   => [ 'matches' => 'number' ],
    'integer'  => [ 'sanitize'  => 'int', 'matches' => 'int', 'max' => PHP_INT_MAX ],
    'float'    => [ 'matches' => 'float' ],
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