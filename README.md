Validation
==========

A validation component, optimized multi-byte (i.e. Japanese language) support.

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

```json
"require": {
    "wscore/validation": "^2.0"
}
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

to validate an array, 

```php
$input->set('name', $input->isText->required()); // set rule on 'name'.  
$age = $input->get('age', $input->isInteger->range([10, 70])); // set rule on age and get the value. 
if ($input->fails()) {
    $messages = $input->messages(); // get error messages
    $badInputs = $input->getAll(); // get all the value including invalidated one. 
}
$goodInputs = $input->getSafe(); // get only the validated value. 
```

* The two basic methods, `set` sets rules, and `get` sets the rule and returns a value. 
* Check the validation result by `fails()` method (or `passes()` method).
* The `getAll()` method retrieves all the validated as well as invalidated values.
  To retrieve __only the validated values__, use ```getSafe()``` method.


### types

Use `set` method with `is{Type}` property, or use `as{Type}($name)` method 
as a shorthand method to set validation rules for the input `$name`. 

```php
$input->set('name', $input->isText->required()); // proper code.
$input->asText('name')->required();                // shorter code.
$input->asMail('mail')->required()->confirm('mail2'));
```

The rule **types** are standard rules for the given validation type. 

The predefined types are:

* binary
* text
* mail
* number
* integer
* float
* date
* dateYM
* datetime
* time
* timeHi
* tel
* fax

which are defined in `Locale/{locale}/validation.types.php` file.

### validation rules

There are many rules for validations. You can chain them 
as shown in previous example codes;

```php
$input->asMail('mail')->required()->string(Rules::STRING_LOWER)->confirm('mail2'));
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

### validating array as input

Validating an array of data is easy. When the validation fails,
 it returns the error message as array.

```php
$input->source( array( 'list' => [ '1', '2', 'bad', '4' ] ) );
$input->asInteger('list')->array();
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

to treat separate input fields as one input, such as date. 

```php
$input->source( [ 'bd_y' => '2001', 'bd_m' => '09', 'bd_d' => '25' ] );
echo $validation->is( 'bd', Rules::date() ); // 2001-09-25
```

use ```multiple``` rules to construct own multiple inputs as,

```php
// for inputs like,
// [ 'ranges_y1' => '2001', 'ranges_m1' => '09', 'ranges_y2' => '2011', 'ranges_m2' => '11' ]
$input->asText('ranges')->multiple( [
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
echo $validation->asText('text1')
	->string('lower')
	->confirm('text2') ); // 123abc
```

Please note that the actual input strings are different.
They become identical after lowering the strings.


### order of filter

some filter must be applied in certain order... 

```php
echo $input->get( 'ABC', $input->isText->pattern('[a-c]*')->string('lower'); // 'abc'
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


Predefined Messages
-------------------

Error message is determined as follows:

1.   message to specify by message rule,
2.   method and parameter specific message,
3.   method specific message,
4.   type specific message, then,
5.   general message

### example 1) message to specify by message rule

for tailored message, use `message` method to set its message.

```php
$input->set('text', $input->isText->required()->message('Oops!'));
echo $input->getMessage('text'); // 'Oops!'
```

### example 2) method and parameter specific message

filter, `matches` has its message based on the parameter. 

```php
$input->set('text', $input->isText->matches('code'));
echo $input->getMessage('code'); // 'only alpha-numeric characters'
```

### example 3 ) method specific message

filters such as `required` and `sameWith` has message.
And lastly, there is a generic message for general errors. 

```php
$validate->verify( 'text', $rule('text')->required() );
echo $input->getMessage('text'); // 'required input'
```

### example 4) type specific message

```php
$input->set('text', $input->isDate->required());
echo $input->getMessage('date'); // 'invalid date'
```

### example 5) general message

uses generic message, if all of the above rules fails.

```php
$input->set('text', $input->pattern('[abc]'));
echo $input->getMessage('text'); // 'invalid input'
```
