<?php
namespace WScore\Validation\Utils;

class Message
{
    /**
     * @var array
     */
    public $messages = array();

    // +----------------------------------------------------------------------+
    /**
     * @param string $locale
     * @param string $dir
     */
    public function __construct($locale = '', $dir = '')
    {
        if (!$locale) {
            $locale = 'en';
        }
        if (!$dir) {
            $dir = dirname(__DIR__) . '/Locale/';
        }
        $dir .= $locale . '/';

        /** @noinspection PhpIncludeInspection */
        $this->setMessages(include($dir . "validation.messages.php"));
    }

    /**
     * @param $messages
     */
    protected function setMessages($messages)
    {
        $this->messages = $messages;
    }

    /**
     * find messages based on error type.
     * 2. use message for a method/parameter set.
     * 3. use message for a specific method.
     * 4. use message for a type.
     * 5. use general error message.
     *
     * @param $type
     * @param $method
     * @param $parameter
     * @return string
     */
    public function find($type, $method, $parameter)
    {
        if (strpos($method, '::filter_') !== false) {
            $method = substr($method, strpos($method, '::filter_') + 9);
        }
        if ($message = $this->findForMethod($method, $parameter)) {
            return $message;
        }
        // 4. use message for a specific type. 
        if (isset($this->messages['_type_'][$type])) {
            return $this->messages['_type_'][$type];
        }

        // 5. use general error message.
        return Helper::arrGet($this->messages, '0', '');
    }

    /**
     * @param string $method
     * @param string $parameter
     * @return string|null
     */
    private function findForMethod($method, $parameter)
    {
        $message = Helper::arrGet($this->messages, $method, null);
        if (!$message) {
            return null;
        }
        // 2. use message for a method/parameter set.
        if (is_array($message)) {
            return Helper::arrGet($message, $parameter, null);
        }
        // 3. use message for a specific method.
        return $message;
    }
    // +----------------------------------------------------------------------+
}