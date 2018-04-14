<?php


namespace umulmrum\JsonParser\Value;


class NullValue implements ValueInterface
{
    /**
     * @var NullValue
     */
    private static $instance;

    /**
     * @return NullValue
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new NullValue();
        }

        return self::$instance;
    }

    private function __construct()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function getValue()
    {
        return null;
    }
}