<?php

namespace umulmrum\JsonParser\State;

use umulmrum\JsonParser\DataSource\DataSourceInterface;
use umulmrum\JsonParser\InvalidJsonException;
use umulmrum\JsonParser\Value\ObjectElementValue;
use umulmrum\JsonParser\Value\ObjectListValue;
use umulmrum\JsonParser\Value\ValueInterface;

class ArrayState implements StateInterface
{
    use WhitespaceTrait;

    /**
     * {@inheritdoc}
     */
    public function run(DataSourceInterface $dataSource): ?ValueInterface
    {
        $values = new ObjectListValue();
        $key = 0;
        $isNextValue = false;
        while (null !== $char = $dataSource->read()) {
            if (true === $this->isWhitespace($char)) {
                continue;
            }
            switch ($char) {
                case ',':
                    if (true === $isNextValue) {
                        InvalidJsonException::trigger('Unexpected character ",", expected value', $dataSource);
                    }
                    $isNextValue = true;
                    break;
                case ']':
                    if (true === $isNextValue) {
                        InvalidJsonException::trigger('Unexpected character "]", expected value', $dataSource);
                    }

                    return $values;
                default:
                    $isNextValue = false;
                    $dataSource->rewind();
                    $currentValue = new ObjectElementValue();
                    $currentValue->setKey($key);
                    $currentValue->setValue(States::$VALUE->run($dataSource));
                    $values->addValue($currentValue);
                    ++$key;
                    break;
            }
        }

        InvalidJsonException::trigger('Unexpected end of data, end of array expected', $dataSource);
    }
}
