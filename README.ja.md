Validation
==========

マルチバイト（主に日本語）サポートが豊富なValidationコンポーネント。

*   コード補完との相性がよいこと、
*   沢山のバリデーションタイプがデフォルトで存在する、
*   そしてマルチバイトキャラクター（日本語のこと）を扱いやすい、
*   ロジックを書きやすい。


### ライセンス

MIT License

### PSR

PSR-1, PSR-2, and PSR-4. 

### インストール

コンポーザーを使ってください。

```sh
composer require "wscore/validation": "^2.0"
```


簡単な使い方
----------

コンポーネントの使い方は、**大体**、こんな感じです。

### Factoryクラス（Validationオブジェクトの生成）

コンポーネント内の ```ValidationFactory``` クラスを使ってバリデーション用オブジェクトを生成してください。

```php
use \WScore\Validation\ValidationFactory;

$factory = new ValidationFactory();    // use English rules and messages.
$factory = new ValidationFactory('ja'); // use Japanese rules and messages.

$input = $factory->on($_POST); // create a validator object. 
```


### 入力データのバリデーション

バリデーションのサンプルコードです。

```php
// set rules for input data. 
$input->set('name')->asText()->required());  // a text
$input->set('type')->asText()->in('old', 'young', 'teenager'); // a selected text
$input->set('age')->asInteger()->range([10, 70]);  // an integer between 10-70

// maybe update rules based on age?
$age = $input->get('age'); 
if ($age > 12) {
    $input->getRules('type')->required();
} else {
    $input->setValue('type', 'teenager');
}
// result of validation. 
if ($input->fails()) {
    $messages   = $input->messages(); // get error messages
    $badInputs  = $input->getAll(); // get all the value including invalidated one. 
    $safeInputs = $input->getSafe(); // get only the validated value. 
} else {
    $validatedInputs  = $input->getAll(); // validation success! 
}
```

* `set($key)`： `$key`に対してバリデーションルールを設定する。
* `get($key)`： バリデートされた値を取得する。バリデーションに失敗した場合はfalse。
* `getRules($key)`： `$key`のバリデーションルールを取得する。
* `fails()`、`passes()`： 全バリデーションの実行結果を取得する。
* `getAll()`： 全ての入力値を返す。バリデーションに失敗した値も含むので注意。 
   一方で、`getSafe()` は __バリデートされた値のみ__ を返す。
* `getMessages()`： バリデーションのエラーメッセージを返す。 



### バリデーションtype

幾つかのtypeについて、標準バリデーションルールが設定されています。
`set` メソッドの直後に、`as{Type}()`を使ってtypeを指定してから、残りのルールを設定します。


```php
$input->set('key')->as{Type}()->{validationRule}();
```

まずは、HTML5と互換性のあるtypeです。

* binary
* text
* mail
* number
* integer
* float
* date
* datetime
* month
* tel

ちょっと変わった、複数の値をまとめたtypeです。  

* dateYMD
* dateYM
* dateHis
* timeHis
* timeHi

これらのtypeは、`Locale/{locale}/validation.types.php`ファイルに定義されています。

### ルール

大量のバリデーションのルールがあります。全て、チェーンして指定可能です。

```php
$input->set('mail')->asMail()->required()->string(Rules::STRING_LOWER)->confirm('mail2'));
```

まずは「フィルター」、つまり値を変更する場合のあるルールです。

 * `message(string $message)`:     エラーメッセージを指定します。
 * `multiple(array $parameter)`:   複数の値をまとめる設定です（例：y,m,dなど）。 
 * `array()`:                      配列入力に対してバリデーションを行います。
 * `noNull(bool $not = true)`:     nullキャラクターを削除します。
 * `encoding(string $encoding)`:   文字コードをバリデートします（デフォルトはUTF-8）。 
 * `mbConvert(string $type)`:      カタカナ・ひらがなを変換します。
    * `mbToHankaku()`: 全角の英数字を半角に変換します。 
    * `mbToZenkaku()`: 半角の英数字を全角に変換します。
    * `mbToHankakuKatakana()`: 全角カタカナを半角カタカナに変換します。 
    * `mbToHiragana()`: カタカナを全角ひらがなに変換します。 
    * `mbToKatakana()`: ひらがなを全角カタカナに変換します。 
 * `trim(bool $trim = true)`:      文字列をtrimします。 
 * `sanitize(string $type)`:       サニテーションを行います。
    * `sanitizeMail()`: メールのサニテーションを行います。
    * `sanitizeFloat()`:  数値のサニテーションを行います。
    * `sanitizeInt()`: 整数のサニテーションを行います。 
    * `sanitizeUrl()`: URLのサニテーションを行います。 
    * `sanitizeString()`: 文字列のサニテーションを行います。 
 * `string(string $type)`:         文字列をupper/lowerなどに変換します。
    * `strToLower()`: 文字列を小文字に変換します。
    * `strToUpper()`: 文字列を大文字に変換します。
    * `strToCapital()`: 文字列をキャピタライズします。
 * `default(string $value)`:       未入力の場合のデフォルト値を指定します。 

値のチェックのみを行うベリファイルールです。

 * `required(bool $required = true)`:          入力必須の項目とします。
 * `requiredIf(string $key, array $in=[])`:    もし$keyが存在（あるいは$inの中の値）の場合に入力必須とします. 
 * `loopBreak(bool $break = true)`:            以降のフィルターを評価しません。
 * `code(string $type)`:           
 * `maxLength(int $length)`:           最大の文字列を指定します。
 * `pattern(string $reg_expression)`:  正規表現によるマッチングを行います。
 * `matches(string $match_type)`:      指定済みの正規表現をマッチさせます（数値、整数、浮動小数点、コード、メール）。 
    このルールを用いると、より的確なエラーメッセージを表示することができます。 
    * `matchNumber()`: 数値の正規表現。
    * `matchInteger()`: 整数の正規表現。 
    * `matchFloat()`: 浮動小数点の正規表現。 
    * `matchCode()`: "[-_0-9a-zA-Z]"の正規表現。 
    * `matchMail()`: 単純なメールの正規表現。 
 * `kanaType(string $match_type)`:     ひらがな・カタカナのチェック。
    * `mbOnlyKatakana()`: 全角カタカナのみかどうか。 
    * `mbOnlyHiragana()`: 全角ひらがなのみかどうか。 
    * `mbOnlyHankaku()`: 半角文字だけかどうか。 
    * `mbOnlyHankakuKatakana()`: 半角カタカナのみかどうか。 
 * `min(int $min)`:                    最小値を指定します。
 * `max(int $max)`:                    最大値を指定します。
 * `range(array $range)`:              値の幅を指定します [min, max].
 * `datetime(string|bool $format = true)`:    日付のフォーマットチェックをします。 
 * `in(array $choices)`:               とりうる値を指定します。 
 * `inKey(array $choices)`:            とりうる値をキーで指定します。 
 * `confirm(string $key)`:             別の項目と同じ値かどうかをチェックします。 


その他の高度な機能
--------------

### 配列のバリデーション

入力が配列の場合でも対応できます。エラーメッセージも配列になります。

```php
$input->source(array('list' => [ '1', '2', 'bad', '4' ]));
$input->set('list')->asInteger()->array()->required();
if ($input->fails()) {
    $values = $validation->get('list');
    $goods  = $validation->getSafe();
    $errors = $validation->message();
}
/*
 * $values = [ '1', '2', '', '4' ];
 * $goods  = array('list' => [ '1', '2', '4' ]);
 * $errors = array('list' => [ 2 => 'required item' ]);
 */
```

### 複数フィールドの入力

例えば日付のように、複数に分割された入力を一つのように扱えます。

```php
$input->source( [ 'bd_y' => '2001', 'bd_m' => '09', 'bd_d' => '25' ] );
echo $validation->is( 'bd', Rules::date() ); // 2001-09-25
```

自作の複数フィールドの入力フィルターを作る場合は、`multiple` を使います。

```php
$input->asText('ranges')->multiple( [
    'suffix' => 'y1,m1,y2,m2',
    'format' => '%04d/%02d - %04d/%02d'
] );
```

ここで、```suffix``` は入力の最後のサフィックス部分、
そして ```format``` が配列を文字列に変換するフォーマット
（sprintfを利用）になります。.


### 入力を比較する（confirm）

パスワードやメールアドレスを入力する際に、
別項目として入力された値と比較することがあります。

```php
$input->source([ 'text1' => '123ABC', 'text2' => '123abc' ] );
echo $validation->asText('text1')
	->string('lower')
	->confirm('text2') ); // 123abc
```


### フィルターの順番

チェックを行う前に、フィルターする必要がありますよね…

```php
echo $validate->verify( 'ABC', Rules::text()->pattern('[a-c]*')->string('lower'); // 'abc'
## should lower the string first, then check for pattern...
```



### 自作バリデーションフィルター

自作のフィルターを利用するには `closure` を利用します。

```php
/**
 * @param ValueTO $v
 */
$filter = function( $v ) {
    $val = $v->getValue();
    $val .= ':customized!';
    $v->setValue( $val );
    $v->setError(__METHOD__);
    $v->setMessage('Closure with Error');
};
Rules::text()->custom( $filter );
```

自作フィルターにはクロージャー以外のパラメターは渡せません
（クロージャー自体がパラメタです）。引数はひとつで、`ValueTO`
オブジェクトになります。これを利用して、値・エラー・メッセージ
などを操作してください。

エラーを設定したら（値は文字なら何でもいいのですが、
__METHOD__` が適当かもしれません）。これでフィルターのループ
が途切れ、これ以降のバリデーションは行いません。

### 値とエラーの設定

バリデーションオブジェクトに対して、任意の値とエラー設定できます。
`setValue` あるいは `setError` を利用します。 
 
```php
$input->setValue('extra', 'good'); // set some value.
$input->setError('bad', 'why it is bad...');  // set error. 
if ($input->fails()) {
    echo $input->getAll()['extra'];  // 'good' 
    echo $input->getMessages('bad'); // 'why it is bad...'
}
```

エラーを設定したら、`fails()`は`true`を返すようになります。 


デフォルトのエラーメッセージ
----------------------

エラーメッセージをカスタマイズするには、まず`your/path/<locale>/`ディレクトリを作成します。
次に

* `validation.filters.php`
* `validation.messages.php`
* `validation.types.php`

のファイルを作成（既存のファイルからコピー）した上で、次のようにします。

```php
$factory = new ValidationFactory('locale', 'your/path');
$input = $factory->on($_POST);
``` 

標準のメッセージは`validation.messages.php` ファイルの配列として定義されています。

```php
return array(
    // 5. general error message（汎用メッセージ）
    0           => 'invalid input',
    // 4. messages for types（ルールタイプで指定されたメッセージ）
    '_type_'    => [
        'mail'     => 'invalid mail format',
        ...
    ],
    // 3. specific messages for method（ルールで指定されたメッセージ）
    'encoding'  => 'invalid encoding',
    ...
    // 2. message for matches and parameter（ルールとパラメターで指定されたメッセージ）
    'matches'   => [
        'number' => 'only numbers (0-9)',
        ...
    ],
);
```

エラーメッセージを決定する手順です。

1.   `message`ルールで指定されたメッセージ、
2.   ルールとパラメターで指定されたメッセージ、
3.   ルールで指定されたメッセージ、
4.   ルールタイプで指定されたメッセージ、
5.   汎用メッセージ。


#### 1. use `message` rule

`message` ルールでメッセージを指定する。

```php
$input->set('text')->asText()->required()->message('Oops!'));
echo $input->getMessage('text'); // 'Oops!'
```

#### 2. method and parameter specific message

`matches` あるいは `kanaType`　ルールは、パラメーターを元にメッセージが指定されます。

```php
$input->set('int')->asText()->matchInteger();
$input->set('kana')->asText()->mbOnlyKatakana();
echo $input->getMessage('int'); // 'not an integer'
echo $input->getMessage('kana'); // 'only in katakana'
```

#### 3. method specific message

`required`などはルールごとにメッセージが存在します。

```php
$input->set('text')->asText()->required();
echo $input->getMessage('text'); // 'required item'
```

#### 4. type specific message

ルールタイプでメッセージが指定されています。

```php
$input->set('date')->asDate();
echo $input->getMessage('date'); // 'invalid date'
```

#### 5. general message

上記のどれにも該当しない場合は、一般的なメッセージを使います。

```php
$input->set('text')->asText()->pattern('[abc]');
echo $input->getMessage('text'); // 'invalid input'
```

