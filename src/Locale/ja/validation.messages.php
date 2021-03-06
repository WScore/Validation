<?php
namespace WScore\Validation\Locale;

use WScore\Validation\Rules;

return array(
    // 5. general error message 
    0           => '入力内容を確認して下さい',
    // 4. messages for types
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
    ],
    // 3. specific messages for method
    'encoding'  => '不正な文字コードです',
    'required'  => '必須項目です',
    'in'        => '選択できない値です',
    'inKey'     => '選択できない値です',
    'sameAs'    => '入力内容が一致しません',
    'sameEmpty' => '確認用の項目を入力ください',
    'max'       => '最大値を超えています',
    'min'       => '最小値より小さい値です',
    'range'     => '範囲から外れてます',
    // 2. message for matches and parameter
    'matches' => [
        Rules::MATCH_NUMBER  => '数値のみです(0-9)',
        Rules::MATCH_INTEGER => '整数を入力してください',
        Rules::MATCH_FLOAT   => '数値を入力してください',
        Rules::MATCH_CODE    => '記号（半角英数字）です',
        Rules::MATCH_MAIL    => 'メールアドレスを入力してください',
    ],
    'kanaType' => [
        Rules::ONLY_HANKAKU      => '半角のみで入力してください',
        Rules::ONLY_HANKAKU_KANA => '半角カタカナのみで入力してください',
        Rules::ONLY_HIRAGANA     => 'ひらがなのみで入力してください',
        Rules::ONLY_KATAKANA     => 'カタカナのみで入力してください',
    ],
);
