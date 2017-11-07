<?php
namespace tests\Validation_1_0;

use WScore\Validation\Dio;
use WScore\Validation\ValidationFactory;

require_once( dirname( __DIR__ ) . '/autoloader.php' );

class Dio_Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Dio
     */
    public $validate;

    /**
     * @var ValidationFactory
     */
    private $factory;

    function setup()
    {
        $this->make();
    }
    
    function make($locale='en') {
        $this->factory = new ValidationFactory($locale);
        $this->validate = $this->factory->on();
    }

    function test0()
    {
        $this->assertEquals( 'WScore\Validation\Dio', get_class( $this->validate ) );
    }

    /**
     * @test
     */
    function get_sets_rule_and_returns_evaluated_value()
    {
        $source = ['test' => 'tested', 'more' => 'tested', 'extra' => 'tested'];
        $input  = $this->factory->on($source);
        $this->assertEquals('tested', $input->get('test'));
        $input->set('more')->asText();
        $input->set('extra')->asText();
        $this->assertEquals('tested', $input->get('more'));
        $this->assertEquals('tested', $input->get('extra'));
        $this->assertEquals('', $input->get('none'));
        $this->assertEquals(['more' => 'tested', 'extra' => 'tested'], $input->getAll());
        $this->assertFalse($input->fails());
        $this->assertTrue($input->passes());
    }
    
    /**
     * @test
     */
    function invalidates_required_value_on_empty()
    {
        $source = ['test' => 'tested'];
        $input = $this->factory->on($source);
        $input->set('test')->asText()->required();
        $input->set('none')->asText()->required();

        $this->assertTrue($input->fails());
        $this->assertFalse($input->passes());

        $this->assertEquals($source,  $input->getSafe());
        $source['none'] = '';
        $this->assertEquals($source,  $input->getAll());
        $this->assertEquals(['none' => 'required item'], $input->getMessages());
    }

    /**
     * @test
     */
    function invalidates_bad_pattern_for_get()
    {
        $source = ['test' => 'tested', 'bad' => 'b@d'];
        $input = $this->factory->on($source);
        $input->set('test')->asText()->required();
        $input->set('bad')->asText()->required()->pattern('[a-z]+')->message('bad pattern');

        $this->assertTrue($input->fails());
        $this->assertFalse($input->passes());

        $this->assertEquals($source,  $input->getAll());
        $this->assertEquals(['test' => 'tested'],  $input->getSafe());
        $this->assertEquals(['bad' => 'bad pattern'], $input->getMessages());
    }

    /**
     * @test
     */
    function get_returns_false_on_invalidated_value()
    {
        $source = ['bad' => 'b@d'];
        $input = $this->factory->on($source);

        $input->set('bad')->asText()->required()->pattern('[a-z]+');
        $this->assertFalse($input->get('bad'));
        $this->assertEquals('b@d', $input->getAll()['bad']);
        $this->assertEquals([], $input->getSafe());
    }

    /**
     * @test
     */
    function set_returns_false_on_invalidated_value()
    {
        $source = ['bad' => 'b@d'];
        $input = $this->factory->on($source);

        $input->set('bad')->asText()->required()->pattern('[a-z]+');
        $this->assertFalse($input->get('bad'));
        $this->assertEquals('b@d', $input->getAll()['bad']);
        $this->assertEquals([], $input->getSafe());
    }

    // +----------------------------------------------------------------------+
    //  test for array input
    // +----------------------------------------------------------------------+
    /**
     * @test
     */
    function validates_array_input_with_array_rule()
    {
        $test = array( 'tested', 'more test' );
        $source = array( 'test' => $test );
        $input = $this->factory->on($source );
        $input->set('test')->asText()->array();
        $got = $input->get('test');

        $this->assertEquals( $test, $got );
        $this->assertEquals( $test, $input->get('test') );
        $this->assertEquals( $source, $input->getAll() );
        $this->assertFalse($input->fails());
        $this->assertTrue($input->passes());
    }

    /**
     * @test
     */
    function validates_array_input_without_array_rule_fails()
    {
        $test = array( 'tested', 'more test' );
        $source = array( 'test' => $test );
        $input = $this->factory->on($source );
        $input->set('test')->asText();
        $got = $input->get('test');

        $this->assertEquals( '', $got );
        $this->assertEquals( '', $input->get('test') );
        $this->assertEquals( ['test' => ''], $input->getAll() );
        $this->assertEquals( false, $this->validate->fails() );
        $this->assertEquals( array(), $this->validate->getMessages() );
    }

    /**
     * @test
     */
    function is_return_array_of_errors_if_input_is_an_array()
    {
        $test = array( '123', 'more test', '456' );
        $source = array( 'test' => $test );
        $collect = array( 'test' => array( 0=>'123', 2=>'456') );
        $input = $this->factory->on($source );

        $input->set('test')->asNumber()->array();

        // should return the input
        $this->assertEquals( false, $input->get('test') );
        $this->assertTrue($input->fails());
        $this->assertEquals( $test, $input->getAll()['test'] );
        $this->assertEquals( $collect, $input->getSafe());

        // validation should become inValid.
        $errors =  $input->getMessages();
        $this->assertEquals( ['test' => [1 => 'only numbers (0-9)']], $errors );
    }

    // +----------------------------------------------------------------------+
    //  test for multiple input
    // +----------------------------------------------------------------------+
    /**
     * @test
     */
    function find_multiple_date_value()
    {
        $source = array( 'test_y'=>'2013', 'test_m'=>'11', 'test_d'=>'08' );
        $this->validate->source($source );
        $this->validate->set( 'test' )->asDate();
        $got = $this->validate->get('test');

        $this->assertEquals( '2013-11-08', $got );
        $this->assertEquals( '2013-11-08', $this->validate->get('test' ) );
        $this->assertEquals(['test' => '2013-11-08'], $this->validate->getAll() );
        $this->assertEquals( true, $this->validate->passes() );
        $this->assertEquals([], $this->validate->getMessages() );
    }

    /**
     * @test
     */
    function find_multiple_datetime_value()
    {
        $source = array( 'test_y'=>'2013', 'test_m'=>'11', 'test_d'=>'08', 'test_h'=>'15', 'test_i'=>'13', 'test_s'=>'59' );
        $this->validate->source($source );
        $this->validate->set( 'test' )->asDatetime();
        $got = $this->validate->get('test');

        $this->assertEquals( '2013-11-08 15:13:59', $got );
        $this->assertEquals( '2013-11-08 15:13:59', $this->validate->get('test' ) );
        $this->assertEquals(['test' => '2013-11-08 15:13:59'], $this->validate->getAll() );
        $this->assertEquals( false, $this->validate->fails() );
        $this->assertEquals([], $this->validate->getMessages() );
    }

    // +----------------------------------------------------------------------+
    //  test for sameWith rule
    // +----------------------------------------------------------------------+
    /**
     * @test
     */
    function is_finds_mail()
    {
        $source = array(
            'mail1' => 'Email@Example.com'
        );
        $mail = 'email@example.com';
        $this->validate->source($source );
        $this->validate->set( 'mail1' )->asMail();
        $got = $this->validate->get('mail1');

        $this->assertEquals( 'email@example.com', $got );
        $this->assertEquals( 'email@example.com', $this->validate->get('mail1' ) );
        $this->assertEquals( array( 'mail1'=>$mail ), $this->validate->getAll() );
        $this->assertEquals( false, $this->validate->fails() );
        $this->assertEquals( array(), $this->validate->getMessages() );
    }
    
    /**
     * @test
     */
    function sameWith_checks_for_another_input()
    {
        $source = array(
            'mail1' => 'Email@Example.com',
            'mail2' => 'EMAIL@example.com'
        );
        $mail = 'email@example.com';
        $this->validate->source($source );
        $this->validate->set( 'mail1' )->asMail()->sameWith( 'mail2');
        $got = $this->validate->get('mail1');

        $this->assertEquals( 'email@example.com', $got );
        $this->assertEquals( 'email@example.com', $this->validate->get('mail1' ) );
        $this->assertEquals( array( 'mail1'=>$mail ), $this->validate->getAll() );
        $this->assertEquals( false, $this->validate->fails() );
        $this->assertEquals( array(), $this->validate->getMessages() );
    }

    /**
     * @test
     */
    function sameWith_fails_if_nothing_to_compare()
    {
        $source = array(
            'mail1' => 'Email@Example.com',
        );
        $mail = 'email@example.com';
        $this->validate->source($source );
        $this->validate->set( 'mail1' )->asMail()->confirm( 'mail2');
        $got = $this->validate->get('mail1');

        $this->assertEquals( false, $got );
        $this->assertEquals( false, $this->validate->get('mail1' ) );
        $this->assertEquals( array( 'mail1'=>$mail ), $this->validate->getAll() );
        $this->assertEquals( true, $this->validate->fails() );
        $this->assertEquals( array( 'mail1'=>'missing value to compare' ), $this->validate->getMessages() );
    }

    /**
     * @test
     */
    function sameWith_fails_if_not_the_same()
    {
        $source = array(
            'mail1' => 'Email@Example.com',
            'mail2' => 'Email2@Example.com',
        );
        $mail = 'email@example.com';
        $this->validate->source($source );
        $this->validate->set( 'mail1' )->asMail()->confirm( 'mail2');
        $got = $this->validate->get('mail1');

        $this->assertEquals( false, $got );
        $this->assertEquals( false, $this->validate->get('mail1' ) );
        $this->assertEquals( array( 'mail1'=>$mail ), $this->validate->getAll() );
        $this->assertEquals( true, $this->validate->fails() );
        $this->assertEquals( array( 'mail1'=>'value not the same' ), $this->validate->getMessages() );
    }

    /**
     * @test
     */
    function sameWith_NOT_fails_if_input_is_missing()
    {
        $source = array(
            'mail1' => '',
            'mail2' => 'Email2@Example.com',
        );
        $this->validate->source($source );
        $this->validate->set( 'mail1' )->asMail()->sameWith( 'mail2');
        $got = $this->validate->get('mail1');

        $this->assertEquals( '', $got );
        $this->assertEquals( '', $this->validate->get('mail1' ) );
        $this->assertEquals( array( 'mail1'=>'' ), $this->validate->getAll() );
        $this->assertEquals( false, $this->validate->fails() );
        $this->assertEquals( array(), $this->validate->getMessages() );
    }

    /**
     * @test
     */
    function sameWith_fails_on_required_item()
    {
        $source = array(
            'mail1' => '',
            'mail2' => 'Email2@Example.com',
        );
        $this->validate->source($source );
        $this->validate->set( 'mail1' )->asMail()->sameWith( 'mail2')->required();
        $got = $this->validate->get('mail1');

        $this->assertEquals( '', $got );
        $this->assertEquals( '', $this->validate->get('mail1' ) );
        $this->assertEquals( array( 'mail1'=>'' ), $this->validate->getAll() );
        $this->assertEquals( true, $this->validate->fails() );
        $this->assertEquals( array( 'mail1'=>'required item' ), $this->validate->getMessages() );
    }

    /**
     * @test
     */
    function multiple_input()
    {
        $input = [ 'a_y1'=>'2014', 'a_m1'=>'05', 'a_d1'=>'01', 'a_y2'=>'2014', 'a_m2'=>'07' ];
        $this->validate->source($input);
        $this->validate->set( 'a' )->asText()->multiple( [
            'suffix' => 'y1,m1,y2,m2',
            'format' => '%04d/%02d - %04d/%02d'
        ] )->required();
        $found = $this->validate->get('a');
        $this->assertEquals( '2014/05 - 2014/07', $found );
    }

    /**
     * @test
     */
    function requiredIf_simple_case()
    {
        $input = [
            'flag' => 'a',
            'done' => '',
        ];
        $this->validate->source($input);
        $this->validate->set('flag')->asText();
        $this->validate->set('done')->asText()->requiredIf('flag');
        $this->assertEquals( true, $this->validate->fails() );
        $this->assertEquals( 'required item', $this->validate->getMessages('done') );
    }

    /**
     * @test
     */
    function requiredIf_with_possible_values()
    {
        $input = [
            'flag' => 'a',
            'done' => '',
        ];
        $this->validate->source($input);
        $this->validate->set('flag')->asText();
        $this->validate->set('done')->asText()->requiredIf('flag', ['a']);
        $this->assertEquals( true, $this->validate->fails() );
        $this->assertEquals( 'required item', $this->validate->getMessages('done') );
    }

    /**
     * @test
     */
    function requiredIf_without_possible_values()
    {
        $input = [
            'flag' => 'a',
            'done' => '',
        ];
        $this->validate->source($input);
        $this->validate->set('flag')->asText();
        $this->validate->set('done')->asText()->requiredIf('flag', ['b']);
        $this->assertEquals( true, $this->validate->passes() );
    }

    /**
     * @test
     */
    function requiredIf_after_rule_is_applyed()
    {
        $input = [
            'flag' => 'A',
            'done' => '',
        ];
        $this->validate->source($input);
        $this->validate->set('flag')->asText();
        $this->validate->set('done')->asText()->requiredIf('flag', ['a']);
        $this->assertEquals( true, $this->validate->passes() );

        $input = [
            'flag' => 'A',
            'done' => '',
        ];
        $validation = $this->factory->on($input);
        $validation->set('flag')->asText()->string('lower');
        $validation->set('done')->asText()->requiredIf('flag', ['a']);
        $this->assertEquals( true, $validation->fails() );
        $this->assertEquals( 'required item', $validation->getMessages('done') );
    }

    /**
     * @test
     */
    function getRules_retrieves_rules()
    {
        $this->validate->source([]);
        $rule1 = $this->validate->set('flag')->asText();
        $rule2 = $this->validate->set('done')->asText()->requiredIf('flag', ['a']);
        
        $this->assertTrue($rule1 === $this->validate->getRule('flag'));
        $this->assertTrue($rule2 === $this->validate->getRule('done'));
        $this->assertTrue(is_null($this->validate->getRule('none')));
    }
}