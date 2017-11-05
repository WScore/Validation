<?php
namespace tests\Validation_1_0;

require_once(dirname(__DIR__).'/autoloader.php');

use WScore\Validation\ValidationFactory;

class Validation_Test extends \PHPUnit_Framework_TestCase
{
    function test0()
    {
        $factory = new ValidationFactory();
        $v = $factory->on( [ 'test' => 'tested' ] );
        $this->assertEquals( 'WScore\Validation\Dio', get_class($v) );

        $v->set('test')->asText();
        $this->assertEquals( 'tested', $v->get('test') );
    }

    function test_locale()
    {
        $factory = new ValidationFactory();
        $v = $factory->on([]);
        $v->set('test')->asText()->required();
        $v->get('test');
        $this->assertEquals( 'required item', $v->getMessages('test') );

        $factory = new ValidationFactory('ja');
        $v = $factory->on([]);
        $v->set('test')->asText()->required();
        $v->get('test');
        $this->assertEquals( '必須項目です', $v->getMessages('test') );
    }

    /**
     * @test
     */
    function reads_messages_from_set_locale_directory()
    {
        $factory = new ValidationFactory('test', __DIR__ . '/LocaleTest/');
        $v = $factory->on([]);
        $v->set('test')->asText()->required();
        $v->get('test');
        $this->assertEquals( 'TESTED: required item', $v->getMessages('test') );

        
        $factory = new ValidationFactory('test', __DIR__ . '/LocaleTest');
        $v = $factory->on([]);
        $v->set('test')->asText()->required();
        $v->get('test');
        $this->assertEquals( 'TESTED: required item', $v->getMessages('test') );
    }

    /**
     * @test
     */
    function filter_invalid_integer_input()
    {
        $factory = new ValidationFactory();
        $v = $factory->on([
            'int' => '100',
            'big' => '101',
            'bad' => '12345678901234567890123456789012345678901234567890',
        ]);
        $v->set('int')->asInteger()->required()->max(100);
        $v->set('big')->asInteger()->required()->max(100);
        $v->set('bad')->asInteger()->required();
        $value1 = (int) $v->get('int');
        $value2 = (int) $v->get('big');
        $value3 = (int) $v->get('bad');

        $this->assertEquals('100',  $value1);
        $this->assertEquals( false, $value2);
        $this->assertEquals( false, $value3);
        $this->assertEquals( 'exceeds max value', $v->getMessages('big') );
        $this->assertEquals( 'required item', $v->getMessages('bad') );
    }

    /**
     * @test
     */
    function filter_min_value()
    {
        $factory = new ValidationFactory();
        $v = $factory->on([
            'int' => '100',
            'big' => '101',
            'bad' => '12345678901234567890123456789012345678901234567890',
        ]);
        $v->set('int')->asInteger()->min(101);
        $v->set('big')->asInteger()->min(101);
        $value1 = $v->get('int');
        $value2 = $v->get('big');

        $this->assertEquals(false,  $value1);
        $this->assertEquals( '101', $value2);
        $this->assertEquals( 'below min value', $v->getMessages('int') );
    }
}