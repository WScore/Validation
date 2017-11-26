<?php
namespace WScore\Validation;

use WScore\Validation\Filter\FilterInterface;
use WScore\Validation\Filter\Filter;
use WScore\Validation\Utils\Message;
use WScore\Validation\Utils\ValueTO;

class ValidationFactory
{
    /**
     * @var string      default locale.
     */
    private $locale = 'en';

    /**
     * @var string      default language directory.
     */
    private $dir = '';

    /**
     * @var FilterInterface
     */
    private $filter;

    /**
     * @param null|string $locale
     * @param null|string $dir
     */
    public function __construct($locale = null, $dir = null)
    {
        $this->setLocale($locale, $dir);
    }

    /**
     * @param null|string $locale
     * @param null|string $dir
     */
    public function setLocale($locale = null, $dir = null)
    {
        $this->locale = $locale ?: $this->locale;
        $this->dir    = $dir ?: __DIR__ . '/Locale/';
        $this->dir    = rtrim($this->dir, '/') . '/';

        $this->factory();
    }

    /**
     * @param FilterInterface $filter
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;
    }

    /**
     * @param array $data
     * @return Dio
     */
    public function on(array $data = [])
    {
        $dio = $this->factory();
        $dio->source($data);

        return $dio;
    }

    /**
     * @return Verify
     */
    public function verify()
    {
        return new Verify(
            $this->filter ?: new Filter(),
            new ValueTO(new Message($this->locale, $this->dir))
        );
    }

    /**
     * @return Dio
     */
    private function factory()
    {
        return new Dio($this->verify(), $this->rules());
    }

    /**
     * @return Rules
     */
    public function rules()
    {
        return new Rules($this->locale, $this->dir);
    }
}