<?php

namespace umulmrum\JsonParser\Value;

class ObjectValue implements ValueInterface
{
    /**
     * @var string|int
     */
    private $key;
    /**
     * @var ValueInterface
     */
    private $value;

    public function __construct($key = null, ValueInterface $value = null)
    {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * @return string|int
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string|int $key
     */
    public function setKey($key): void
    {
        $this->key = $key;
    }

    public function getValue()
    {
//        return [
//            $this->key => $this->value->getValue(),
//        ];
        return $this->value->getValue();
    }

    /**
     * @param ValueInterface $value
     */
    public function setValue(ValueInterface $value): void
    {
        $this->value = $value;
    }
}
