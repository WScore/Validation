<?php
namespace WScore\Validation\Locale;

return array(
    0           => 'TESTED: invalid input',         // general error message 
    'encoding'  => 'TESTED: invalid encoding',      // specific messages for method
    'required'  => 'TESTED: required item',
    'in'        => 'TESTED: invalid choice',
    'sameAs'    => 'TESTED: value not the same',
    'sameEmpty' => 'TESTED: missing value to compare',
    'max'      => 'TESTED: exceeds max value',
    'min'      => 'TESTED: below min value',
    'matches'   => [                        // message for matches and parameter
        'number' => 'TESTED: only numbers (0-9)',
        'int'    => 'TESTED: not an integer',
        'float'  => 'TESTED: not a floating number',
        'code'   => 'TESTED: only alpha-numeric characters',
        'mail'   => 'TESTED: not a valid mail address',
    ],
    '_type_' => [
        'mail'     => 'TESTED: invalid mail format',
        'number'   => 'TESTED: not a number',
        'integer'  => 'TESTED: not an integer',
        'float'    => 'TESTED: not a float',
        'date'     => 'TESTED: invalid date',
        'datetime' => 'TESTED: invalid date-time',
        'dateYM'   => 'TESTED: invalid year-month',
        'time'     => 'TESTED: invalid time',
        'timeHI'   => 'TESTED: invalid hour-minute',
        'tel'      => 'TESTED: invalid tel number',
    ],
);