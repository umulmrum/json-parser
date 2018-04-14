<?php


namespace umulmrum\JsonParser\Value;


class ObjectValues implements ValueInterface
{
    /**
     * @var ObjectValue[]
     */
    private $values = [];

    public function addValue(ObjectValue $value)
    {
        $this->values[] = $value;
    }

    /**
     * @return ObjectValue[]
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * {@inheritDoc}
     */
    public function getValue()
    {
        $values = [];
        foreach ($this->values as $value) {
            $values[$value->getKey()] = $value->getValue()->getValue();
        }

        return $values;
    }
}