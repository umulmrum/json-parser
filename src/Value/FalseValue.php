<?php


namespace umulmrum\JsonParser\Value;


class FalseValue implements ValueInterface
{
    /**
     * @var FalseValue
     */
    private static $instance;

    /**
     * @return FalseValue
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new FalseValue();
        }

        return self::$instance;
    }

    private function __construct()
    {
    }

    public function getValue()
    {
        return false;
    }
}