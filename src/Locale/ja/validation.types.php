<?php

// filters for various types of input.

return array(
    'binary'   => [ 'noNull' => false, 'encoding' => false, 'mbConvert' => false, 'trim' => false ],
    'text'     => [],
    'hiragana' => [ 'mbConvert' => 'hiragana', 'kanaType' => 'hiragana' ],
    'katakana' => [ 'mbConvert' => 'katakana', 'kanaType' => 'katakana' ],
    'mail'     => [ 'mbConvert' => 'hankaku', 'string' => 'lower', 'matches' => 'mail', 'sanitize' => 'mail' ],
    'number'   => [ 'mbConvert' => 'hankaku', 'matches' => 'number' ],
    'integer'  => [ 'sanitize'  => 'int', 'mbConvert' => 'hankaku', 'matches' => 'int', 'max' => PHP_INT_MAX ],
    'float'    => [ 'mbConvert' => 'hankaku', 'matches' => 'float' ],
    'date'     => [ 'mbConvert' => 'hankaku', 'datetime' => 'Y-m-d' ],
    'datetime' => [ 'mbConvert' => 'hankaku', 'datetime' => 'Y-m-d H:i:s' ],
    'month'    => [ 'mbConvert' => 'hankaku', 'datetime' => 'Y-m' ],
    'tel'      => [ 'mbConvert' => 'hankaku', 'pattern' => '[-0-9()]*' ],
    
    'dateYMD'  => [ 'multiple' => 'YMD', 'mbConvert' => 'hankaku', 'datetime' => 'Y-m-d' ],
    'dateYM'   => [ 'multiple' => 'YM',  'mbConvert' => 'hankaku', 'datetime' => 'Y-m' ],
    'dateHis'  => [ 'multiple' => 'datetime', 'mbConvert' => 'hankaku', 'datetime' => 'Y-m-d H:i:s' ],
    'time'     => [ 'multiple' => 'His', 'mbConvert' => 'hankaku', 'datetime' => 'H:i:s' ],
    'timeHi'   => [ 'multiple' => 'Hi',  'mbConvert' => 'hankaku', 'datetime' => 'H:i' ],
);