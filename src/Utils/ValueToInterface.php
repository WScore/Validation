<?php
/**
 * Created by PhpStorm.
 * User: asao
 * Date: 2014/05/30
 * Time: 16:39
 */
namespace WScore\Validation\Utils;


/**
 * value transfer object.
 * Class ValueTO
 *
 * @package WScore\Validation
 */
interface ValueToInterface
{
    /**
     * @return bool
     */
    public function isValue();
    
    /**
     * returns the value. 
     * the value maybe invalid. 
     * 
     * @return mixed
     */
    public function getValue();
    
    /**
     * return the validated value. 
     * returns false if validation fails. 
     * 
     * @return bool|mixed
     */
    public function getValidValue();
    
    /**
     * returns validation type, such as text, mail, date, etc. 
     * 
     * @return string
     */
    public function getType();

    /**
     * gets message regardless of the error state of this ValueTO.
     * use this message ONLY WHEN valueTO is error.
     *
     * @return string|array
     */
    public function message();

    /**
     * @return mixed
     */
    public function __toString();

    /**
     * @return bool
     */
    public function fails();
}