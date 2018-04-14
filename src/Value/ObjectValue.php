<?php


namespace umulmrum\JsonParser\Value;


class ObjectValue implements ValueInterface
{
    /**
     * @var string
     */
    private $key;
    /**
     * @var ValueInterface
     */
    private $value;

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    /**
     * @return ValueInterface
     */
    public function getValue(): ValueInterface
    {
        return $this->value;
    }

    /**
     * @param ValueInterface $value
     */
    public function setValue(ValueInterface $value): void
    {
        $this->value = $value;
    }


}