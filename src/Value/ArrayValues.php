<?php


namespace umulmrum\JsonParser\Value;


class ArrayValues implements ValueInterface
{
    /**
     * @var ArrayValue[]
     */
    private $values = [];

    public function addValue(ArrayValue $value)
    {
        $this->values[] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function getValue()
    {
        $values = [];
        foreach ($this->values as $value) {
            $values[] = $value->getValue();
        }

        return $values;
    }
}