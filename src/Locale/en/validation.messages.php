<?php
namespace WScore\Validation\Locale;

use WScore\Validation\Rules;

return array(
    // 5. general error message 
    0           => 'invalid input',
    // 4. messages for types
    '_type_'    => [
        'mail'     => 'invalid mail format',
        'number'   => 'not a number',
        'integer'  => 'not an integer',
        'float'    => 'not a float',
        'date'     => 'invalid date',
        'datetime' => 'invalid date-time',
        'dateYM'   => 'invalid year-month',
        'time'     => 'invalid time',
        'timeHI'   => 'invalid hour-minute',
        'tel'      => 'invalid tel number',
    ],
    // 3. specific messages for method
    'encoding'  => 'invalid encoding',
    'required'  => 'required item',
    'in'        => 'invalid choice',
    'inKey'     => 'invalid choice',
    'sameAs'    => 'value not the same',
    'sameEmpty' => 'missing value to compare',
    'max'       => 'exceeds max value',
    'min'       => 'below min value',
    // 2. message for matches and parameter
    'matches' => [
        Rules::MATCH_NUMBER  => 'only numbers (0-9)',
        Rules::MATCH_INTEGER => 'not an integer',
        Rules::MATCH_FLOAT   => 'not a floating number',
        Rules::MATCH_CODE    => 'only alpha-numeric characters',
        Rules::MATCH_MAIL    => 'not a valid mail address',
    ],
    'kanaType' => [
        Rules::ONLY_HANKAKU      => 'only ASCII characters',
        Rules::ONLY_HANKAKU_KANA => 'only half-width katakana',
        Rules::ONLY_HIRAGANA     => 'only in hiragana',
        Rules::ONLY_KATAKANA     => 'only in katakana',
    ],
);