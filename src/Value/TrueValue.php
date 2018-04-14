<?php


namespace umulmrum\JsonParser\Value;


class TrueValue implements ValueInterface
{
    /**
     * @var TrueValue
     */
    private static $instance;

    /**
     * @return TrueValue
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new TrueValue();
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
        return true;
    }
}