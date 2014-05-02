<?php
namespace WStest02\Validation;

use WScore\Validation\Input;

require_once( dirname( dirname( __DIR__ ) ) . '/autoloader.php' );

class Input_Test extends \PHPUnit_Framework_TestCase
{
    function setup()
    {
        Input::locale('en');
        Input::forge(true);
    }
    /**
     */
    function test0()
    {
        $object = Input::$input;
        $this->assertEquals( 'WScore\Validation\Validation', get_class( $object ) );
    }
    
    /**
     * @test
     */
    function push_a_single_value()
    {
        $source = array( 'test' => 'tested' );
        Input::source($source);
        $got = Input::push( 'test', 'text' );

        $this->assertEquals( 'tested', $got );
        $this->assertEquals( 'tested', Input::pop( 'test' ) );
        $this->assertEquals( $source, Input::pop() );
        $this->assertEquals( true, Input::isValid() );
        $this->assertEquals( array(), Input::popError() );
    }

    /**
     * @test
     */
    function text_a_single_value()
    {
        $source = array( 'test' => 'tested' );
        Input::source($source);
        $got = Input::text( 'test' );

        $this->assertEquals( 'tested', $got );
        $this->assertEquals( 'tested', Input::pop( 'test' ) );
        $this->assertEquals( $source, Input::pop() );
        $this->assertEquals( true, Input::isValid() );
        $this->assertEquals( array(), Input::popError() );
    }

    // +----------------------------------------------------------------------+
    //  test for array input
    // +----------------------------------------------------------------------+

}