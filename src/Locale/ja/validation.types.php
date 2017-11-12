<?php

// filters for various types of input.

use WScore\Validation\Rules;

return array(
    'binary'   => [ 'noNull' => false, 'encoding' => false, 'mbConvert' => false, 'trim' => false ],
    'text'     => [],
    'hiragana' => [ 'mbConvert' => Rules::MB_HIRAGANA, 'kanaType' => Rules::ONLY_HIRAGANA ],
    'katakana' => [ 'mbConvert' => Rules::MB_KATAKANA, 'kanaType' => Rules::ONLY_KATAKANA ],
    'mail'     => [ 'mbConvert' => Rules::MB_HANKAKU, 'string' => Rules::STRING_LOWER, 'matches' => Rules::MATCH_MAIL, 'sanitize' => 'mail' ],
    'number'   => [ 'mbConvert' => Rules::MB_HANKAKU, 'matches' => Rules::MATCH_NUMBER ],
    'integer'  => [ 'mbConvert' => Rules::MB_HANKAKU, 'matches' => Rules::MATCH_INTEGER, 'sanitize'  => 'int', 'max' => PHP_INT_MAX ],
    'float'    => [ 'mbConvert' => Rules::MB_HANKAKU, 'matches' => Rules::MATCH_FLOAT ],
    'date'     => [ 'mbConvert' => Rules::MB_HANKAKU, 'datetime' => 'Y-m-d' ],
    'datetime' => [ 'mbConvert' => Rules::MB_HANKAKU, 'datetime' => 'Y-m-d H:i:s' ],
    'month'    => [ 'mbConvert' => Rules::MB_HANKAKU, 'datetime' => 'Y-m' ],
    'tel'      => [ 'mbConvert' => Rules::MB_HANKAKU, 'pattern' => '[-0-9()]*' ],
    
    'dateYMD'  => [ 'multiple' => 'YMD', 'mbConvert' => 'hankaku', 'datetime' => 'Y-m-d' ],
    'dateYM'   => [ 'multiple' => 'YM',  'mbConvert' => 'hankaku', 'datetime' => 'Y-m' ],
    'dateHis'  => [ 'multiple' => 'datetime', 'mbConvert' => 'hankaku', 'datetime' => 'Y-m-d H:i:s' ],
    'time'     => [ 'multiple' => 'His', 'mbConvert' => 'hankaku', 'datetime' => 'H:i:s' ],
    'timeHi'   => [ 'multiple' => 'Hi',  'mbConvert' => 'hankaku', 'datetime' => 'H:i' ],
);