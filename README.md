Validation
==========

A validation component, designed for multi-byte (i.e. Japanese language) support.
Features includes, such as:

*   works well with code completion.
*   multiple values combined to a single value (ex: bd_y, bd_m, bd_d to bd).
*   preset order of rules to apply. essential to handle Japanese characters.
*   easy to code logic.


### License

MIT License

### PSR

PSR-1, PSR-2, and PSR-4. 

### Installation

```sh
composer require "wscore/validation": "^2.0"
```


Basic Usage
------------

### factory object

Use `ValidationFactory` to start validating an array.
For instance, 

```php
use \WScore\Validation\ValidationFactory;

$factory = new ValidationFactory();    // use English rules and messages.
$factory = new ValidationFactory('ja'); // use Japanese rules and messages.

$input = $factory->on($_POST); // create a validator object. 
```

### validation

a sample code for validating an array, 

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

* `set($key)` sets validation rules for `$key`.
* `get($key)` returns a validated value, or false if validation fails.
* `getRules($key)` returns an existing rules object to modify.
* `fails()` or `passes()` method will evaluate all rules and 
   returns boolean if all validation rules has passed or not.
* `getAll()` returns all the value including the invalidated ones, 
   whereas `getSafe()` returns __only the validated values__.
* `getMessages()` returns all the invalidated error messages. 

### types

The rule **types** are standard rules for the given validation type. 
Select validation type by `as{Type}()` after `set` method, 

```php
$input->set('key')->as{Type}()->{validationRule}();
```

The predefined types are:

* binary
* text
* mail
* number
* integer
* float
* date
* datetime
* month
* dateYMD
* dateYM
* dateHis
* timeHis
* timeHi
* tel

which are defined in `Locale/{locale}/validation.types.php` file.

### validation rules

There are many rules for validations. You can chain them 
as shown in previous example codes;

```php
$input->set('mail')->asMail()->required()->string(Rules::STRING_LOWER)->confirm('mail2'));
```

Available filter (or rules) types. 

 * `message(string $message)`:     set error message.
 * `multiple(array $parameter)`:   set multiple field inputs, such as Y, m, and d. 
 * `array()`:                      allows array input.
 * `noNull(bool $not = true)`:     removes null character. 
 * `encoding(string $encoding)`:   validates on character encoding (default is UTF-8). 
 * `mbConvert(string $type)`:      converts kana-set (in Japanese). 
 * `trim(bool $trim = true)`:      trims input string. 
 * `sanitize(string $type)`:       sanitize value. 
 * `string(string $type)`:         converts string to upper/lower/etc. 
 * `default(string $value)`:       sets default value if not set. 
 * `required(bool $required = true)`:          required value
 * `requiredIf(string $key, array $in=[])`:    set required if $key exists (or in $in). 
 * `loopBreak(bool $break = true)`:            breaks filter loop. 
 * `code(string $type)`:           
 * `maxLength(int $length)`:           maximum character length. 
 * `pattern(string $reg_expression)`:  preg_match pattern
 * `matches(string $match_type)`:      preset regex patterns (number, int, float, code, mail).
 * `kanaType(string $match_type)`:     checks kana-type ().
 * `min(int $min)`:                    minimum numeric value.
 * `max(int $max)`:                    maximum numeric value.
 * `range(array $range)`:              range of [min, max].
 * `datetime(string|bool $format = true)`:    checks for datetime with format. 
 * `in(array $choices)`:               checks for list of possible values. 
 * `confirm(string $key)`:             confirm against another $key. 


Advanced Features
-----------------

### validating a list of input

To validate a list of input data, such as checkbox, use `array()` rules as follows. 
When the validation fails, it returns the error message as array.

```php
$input->source( array( 'list' => [ '1', '2', 'bad', '4' ] ) );
$input->set('list')->asInteger()->array();
if( $input->fails() ) {
    $values = $validation->get('list');
    $goods  = $validation->getSafe();
    $errors = $validation->message();
}
/*
 * $values = [ '1', '2', 'bad', '4' ];
 * $goods  = array( 'list' => [ '1', '2', '4' ] );
 * $errors = array( 'list' => [ 2 => 'not an integer' ] );
 */
```


### multiple inputs

`WScore/Validation` can handle separate input fields, such as date, as one input. 
For instance, `date`, `dateYM`, `datetime` types 

```php
$input->source( [ 'bd_y' => '2001', 'bd_m' => '09', 'bd_d' => '25' ] );
echo $validation->set('bd')->asDate(); // 2001-09-25
```

use ```multiple``` rules to construct own multiple inputs as,

```php
// for inputs like,
// [ 'ranges_y1' => '2001', 'ranges_m1' => '09', 'ranges_y2' => '2011', 'ranges_m2' => '11' ]
$input->set('ranges')->asText()->multiple( [
    'suffix' => 'y1,m1,y2,m2',
    'format' => '%04d/%02d - %04d/%02d'
] );
```

where `suffix` lists the postfix for the inputs,
and `format` is the format string using sprintf.


### confirm to compare values

for password or email validation with two input fields 
to compare each other. 

```php
$input->source([ 'text1' => '123ABC', 'text2' => '123abc' ] );
echo $validation->set('text1')
    ->asText()
	->string('lower')
	->confirm('text2') ); // 123abc
```

Please note that the actual input strings are different.
They become identical after lowering the strings.


### order of filter

some filter must be applied in certain order... 

```php
echo $input->set('ABC')->asText()->pattern('[a-c]*')->string('lower'); // 'abc'
# must lower the string first, then check for pattern...
```


### custom validation

Use a closure as custom validation filter.

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
$input->asText('test')->addCustom( 'myFilter', $filter );
```

You cannot pass parameter (the closure is the parameter).
argument is the ValueTO object which can be used to handle
error and messages.

setting error with, well, actually, any string,
but `__METHOD__` maybe helpful. this will break the
filter loop, i.e. no filter will be evaluated.

### setting values and errors

To set a value, or an error to the validator, use `setValue` and `setError` methods. 
 
```php
$input->setValue('extra', 'good'); // set some value.
$input->setError('bad', 'why it is bad...');  // set error. 
if ($input->fails()) {
    echo $input->getAll()['extra'];  // 'good' 
    echo $input->getMessages('bad'); // 'why it is bad...'
}
```

Setting error will make `fails()` method to return `true`. 


Modifying Error Messages
------------------

To use your own messages, create a file at `your/path/<locale>/validation.messages.php`, 
then construct the validation as;

```php
$factory = new ValidationFactory('locale', 'your/path');
$input = $factory->on($_POST);
``` 

The `validation.messages.php` should return an array looks like: 

```php
return array(
    // 5. general error message 
    0           => 'invalid input',
    // 4. messages for types
    '_type_'    => [
        'mail'     => 'invalid mail format',
        ...
    ],
    // 3. specific messages for method
    'encoding'  => 'invalid encoding',
    ...
    // 2. message for matches and parameter
    'matches'   => [
        'number' => 'only numbers (0-9)',
        ...
    ],
);
```

whereas the error messages are determined as follows:

1.   message set by `message` rule,
2.   method and parameter specific message,
3.   method specific message,
4.   type specific message, then,
5.   general message


#### 1. use `message` rule

use `message` method to set its message.

```php
$input->set('text')->asText()->required()->message('Oops!'));
echo $input->getMessage('text'); // 'Oops!'
```

#### 2. method and parameter specific message

filter, `matches` has its message based on the parameter. 

```php
$input->set('text')->asText()->matches('code');
echo $input->getMessage('code'); // 'only alpha-numeric characters'
```

#### 3. method specific message

filters such as `required` and `sameWith` has message.
And lastly, there is a generic message for general errors. 

```php
$input->set('text')->asText()->required();
echo $input->getMessage('text'); // 'required input'
```

#### 4. type specific message

```php
$input->set('date')->asDate();
echo $input->getMessage('date'); // 'invalid date'
```

#### 5. general message

uses generic message, if all of the above rules fails.

```php
$input->set('text')->asText()->pattern('[abc]');
echo $input->getMessage('text'); // 'invalid input'
```
