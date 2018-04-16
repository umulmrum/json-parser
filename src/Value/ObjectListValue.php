<?php

namespace umulmrum\JsonParser\Value;

class ObjectListValue implements ValueInterface
{
    /**
     * @var ObjectElementValue[]
     */
    private $valueList = [];

    public function addValue(ObjectElementValue $value)
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
        $firstValue = $this->getFirstValue();
        if (null === $firstValue) {
            return null;
        }

        return $firstValue->getKey();
    }

    public function getFirstValue(): ?ObjectElementValue
    {
        $value = \current($this->valueList);
        if (false === $value) {
            return null;
        }

        return $value;
    }
}
