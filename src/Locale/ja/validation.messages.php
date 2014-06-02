<?php
namespace WScore\Validation\Locale;

return array(
    0           => '入力内容を確認して下さい',
    'encoding'  => '不正な文字コードです',
    'required'  => '必須項目です',
    'in'        => '選択できない値です',
    'sameAs'    => '入力内容が一致しません',
    'sameEmpty' => '確認用の項目を入力ください',
    'matches'   => [
        'number' => '数値のみです(0-9)',
        'int'    => '整数を入力してください',
        'float'  => '数値を入力してください',
        'code'   => '記号（半角英数字）です',
        'mail'   => 'メールアドレスを入力してください',
    ],
    '_type_' => [
        'mail'     => 'メールアドレスが間違ってます',
        'number'   => '半角の数値です',
        'integer'  => '整数を入力してください',
        'float'    => '数値を入力してください',
        'date'     => '正しい日付を入力してください',
        'datetime' => '正しい日時を入力してください',
        'dateYM'   => '正しい年月を入力してください',
        'time'     => '正しい時間を入力してください',
        'timeHI'   => '正しい時分を入力してください',
        'tel'      => '正しい電話番号を入力してください',
    ]
);
