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

    public function getValue(): array
    {
        $result = [];
        foreach ($this->valueList as $value) {
            $result[$value->getKey()] = $value->getValue();
        }

        return $result;
    }

//    /**
//     * {@inheritDoc}
//     */
//    public function getValues()
//    {
//        $values = [];
//        foreach ($this->values as $value) {
//            $values[$value->getKey()] = $value->getValue()->getValue();
//        }
//
//        return $values;
//    }
}
