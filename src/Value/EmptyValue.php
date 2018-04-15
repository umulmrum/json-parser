<?php

namespace umulmrum\JsonParser\Value;

class EmptyValue implements ValueInterface
{
    /**
     * @var EmptyValue
     */
    private static $instance;

    /**
     * @return EmptyValue
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new EmptyValue();
        }

        return self::$instance;
    }

    private function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return null;
    }
}
