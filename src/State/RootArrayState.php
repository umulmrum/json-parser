<?php

namespace umulmrum\JsonParser\State;

use umulmrum\JsonParser\DataSource\DataSourceInterface;
use umulmrum\JsonParser\InvalidJsonException;
use umulmrum\JsonParser\Value\EmptyValue;
use umulmrum\JsonParser\Value\ObjectElementValue;
use umulmrum\JsonParser\Value\ObjectListValue;
use umulmrum\JsonParser\Value\ValueInterface;

class RootArrayState implements StateInterface
{
    use WhitespaceTrait;

    /**
     * {@inheritdoc}
     */
    public function run(DataSourceInterface $dataSource): ?ValueInterface
    {
        $value = new ObjectListValue();
        $element = new ObjectElementValue(0);
        $value->addValue($element);
        while (null !== $char = $dataSource->read()) {
            if (true === $this->isWhitespace($char)) {
                continue;
            }
            if (']' === $char) {
                return EmptyValue::getInstance();
            }
            $dataSource->rewind();

            $element->setValue(States::$VALUE->run($dataSource));

            return $value;
        }

        InvalidJsonException::trigger('Unexpected end of data, end of array expected', $dataSource);
    }
}
