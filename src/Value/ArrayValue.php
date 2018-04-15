<?php


namespace umulmrum\JsonParser\Value;


class ArrayValue implements ValueInterface
{
    /**
     * @var ValueInterface
     */
    private $value;

    public function setValue(ValueInterface $value): void
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value->getValue();
    }

    public function resolve()
    {
        // TODO: Implement resolve() method.
    }
}