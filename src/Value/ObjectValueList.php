<?php

namespace umulmrum\JsonParser\Value;

class ObjectValueList implements ValueInterface
{
    /**
     * @var ObjectValue[]
     */
    private $valueList = [];

    public function addValue(ObjectValue $value)
    {
        $this->valueList[] = $value;
    }

    public function getValueList(): array
    {
        return $this->valueList;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(): array
    {
        $result = [];
        foreach ($this->valueList as $value) {
            $result[$value->getKey()] = $value->getValue();
        }

        return $result;
    }

    public function getFirstKey()
    {
        return \key($this->valueList);
    }

    public function getFirstValue(): ?ObjectValue
    {
        $value = \current($this->valueList);
        if (false === $value) {
            return null;
        }

        return $value;
    }
}
