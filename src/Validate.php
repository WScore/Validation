<?php
namespace WScore\Validation;

/**
 * Class Validate
 * @package WScore\Validation
 */
class Validate
{
    /**
     * @var Filter
     */
    public $filter;

    /**
     * @var ValueTO
     */
    public $valueTO;

    /**
     * @var ValueTO
     */
    protected $lastValue;

    // +----------------------------------------------------------------------+
    //  construction
    // +----------------------------------------------------------------------+
    /**
     * @param Filter  $filter
     * @param ValueTO $valueTO
     */
    public function __construct( $filter=null, $valueTO=null )
    {
        if( isset( $filter  ) ) $this->filter  = $filter;
        if( isset( $valueTO ) ) $this->valueTO = $valueTO;
    }

    /**
     * @param null|string $locale
     * @param null $dir
     * @return static
     */
    public static function getInstance( $locale=null, $dir=null )
    {
        return new static(
            new Filter(), new ValueTO( Message::getInstance( $locale, $dir ) )
        );
    }

    // +----------------------------------------------------------------------+
    //  validation: for a single value.
    // +----------------------------------------------------------------------+
    /**
     * validates a single text value.
     * returns the filtered value, or false if validation fails.
     *
     * @param string $value
     * @param array  $rules
     * @return bool|mixed
     */
    public function is( $value, $rules )
    {
        $this->applyFilters( $value, $rules );
        if( !$this->result()->fails() ) {
            return $this->result()->getValue();
        }
        return false;
    }

    /**
     * @return ValueToInterface
     */
    public function result() {
        return $this->lastValue;
    }

    /**
     * apply filters on a single value.
     *
     * @param string $value
     * @param array $rules
     * @return null|ValueTO
     */
    public function applyFilters( $value, $rules=array() )
    {
        /** @var $filter Filter */
        $valueTO = $this->valueTO->reset( $value );
        // loop through all the rules to validate $value.
        foreach( $rules as $rule => $parameter )
        {
            // some filters are not to be applied...
            if( $parameter === false ) continue; // skip rules with option as FALSE.
            // apply filter.
            $method = 'filter_' . $rule;
            if( method_exists( $this->filter, $method ) ) {
                $this->filter->$method( $valueTO, $parameter );
            } elseif( is_object( $parameter ) && is_callable( $parameter ) ) {
                $this->filter->applyClosure( $valueTO, $parameter );
            }
            // loop break.
            if( $valueTO->getBreak() ) break;
        }
        $this->lastValue = $valueTO;
        return $valueTO;
    }
    // +----------------------------------------------------------------------+
}