<?php
namespace WStest02\Validation;

use WScore\Validation\Verify;
use WScore\Validation\Rules;

require_once( dirname( dirname( __DIR__ ) ) . '/autoloader.php' );

class Validate_Test extends \PHPUnit_Framework_TestCase
{
    /** @var \WScore\Validation\Verify */
    var $validate;

    /** @var  Rules */
    var $rules;

    public function setUp()
    {
        $this->validate = Verify::getInstance();
        $this->rules    = new Rules();
    }

    // +----------------------------------------------------------------------+
    /**
     * @test
     */
    function basic_class_type()
    {
        $this->assertEquals( 'WScore\Validation\Validate', get_class( $this->validate ) );
    }

    // +----------------------------------------------------------------------+
    //  tests on applyFilter methods
    // +----------------------------------------------------------------------+
    /**
     * @test
     */
    function apply_filter_trim()
    {
        $value = $this->validate->applyFilters( ' text ', [ 'trim' => true ] );
        $this->assertEquals( 'WScore\Validation\Validate', get_class( $this->validate ) );
        $this->assertEquals( 'text', $value->getValue() );
        $this->assertEquals( 'text', $value );
        $this->assertEquals( 'invalid input', $value->message() );
    }

    /**
     * @test
     */
    function apply_filter_message()
    {
        $value = $this->validate->applyFilters( 'text', [ 'message' => 'tested' ] );
        $this->assertEquals( 'tested', $value->message() );
    }

    /**
     * @test
     */
    function message_is_set_if_required_fails()
    {
        $value = $this->validate->applyFilters( '', [ 'required' => true ] );
        $this->assertEquals( 'required item', $value->message() );
    }

    // +----------------------------------------------------------------------+
    //  tests on is methods
    // +----------------------------------------------------------------------+
    /**
     * @test
     */
    function is_returns_filtered_value()
    {
        $value = $this->validate->is( ' text ', [ 'trim' => true ] );
        $this->assertEquals( 'text', $value );
    }

    /**
     * @test
     */
    function is_returns_false_if_filer_fails()
    {
        $value = $this->validate->is( '', [ 'required' => true ] );
        $this->assertEquals( false, $value );
    }

    // +----------------------------------------------------------------------+
    //  integrate test using Rules object
    // +----------------------------------------------------------------------+
    /**
     * @test
     */
    function applyFilter_with_rules()
    {
        $rules = $this->rules;
        $value = $this->validate->applyFilters( ' text ', $rules( 'text' ) );
        $this->assertEquals( 'WScore\Validation\Validate', get_class( $this->validate ) );
        $this->assertEquals( 'text', $value->getValue() );
        $this->assertEquals( 'invalid input', $value->message() );
    }

    /**
     * @test
     */
    function is_with_rules()
    {
        $rules = $this->rules;
        $value = $this->validate->is( ' text ', $rules( 'text' ) );
        $this->assertEquals( 'text', $value );
    }

    /**
     * @test
     */
    function isValid_return_true_if_no_error()
    {
        $this->assertEquals( true, $this->validate->isValid() );
        $this->assertEquals( null, $this->validate->getMessage() );
        $this->validate->is( 'text', array( 'trim' => true ) );
        $this->assertEquals( true, $this->validate->isValid() );
        $this->assertEquals( null, $this->validate->getMessage() );
    }

    /**
     * @test
     */
    function getMessage_returns_msg_if_is_fails()
    {
        $this->assertEquals( true, $this->validate->isValid() );
        $this->assertEquals( null, $this->validate->getMessage() );
        $this->validate->is( 'text', array( 'pattern' => '[0-9]*' ) );
        $this->assertEquals( false, $this->validate->isValid() );
        $this->assertEquals( 'invalid input', $this->validate->getMessage() );
    }
}