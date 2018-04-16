<?php

namespace umulmrum\JsonParser\State;

use umulmrum\JsonParser\DataSource\DataSourceInterface;
use umulmrum\JsonParser\InvalidJsonException;
use umulmrum\JsonParser\Value\ObjectElementValue;
use umulmrum\JsonParser\Value\ObjectListValue;
use umulmrum\JsonParser\Value\ValueInterface;

class ObjectState implements StateInterface
{
    use WhitespaceTrait;

    /**
     * {@inheritdoc}
     */
    public function run(DataSourceInterface $dataSource): ?ValueInterface
    {
        $values = new ObjectListValue();
        $keyFound = false;
        $valueFound = false;
        $nextElementRequested = false;
        $currentValue = new ObjectElementValue();

        while (null !== $char = $dataSource->read()) {
            if (true === $this->isWhitespace($char)) {
                continue;
            }
            switch ($char) {
                case '}':
                    if (true === $keyFound && false === $valueFound) {
                        InvalidJsonException::trigger('No value found for key', $dataSource);
                    }
                    if (true === $nextElementRequested) {
                        InvalidJsonException::trigger('Unexpected character "}", expected next element', $dataSource);
                    }

                    return $values;
                case '"':
                    if (true === $keyFound) {
                        InvalidJsonException::trigger('Invalid character \'"\', ":" expected', $dataSource);
                    }
                    $currentValue->setKey(States::$STRING->run($dataSource)->getValue());
                    $keyFound = true;
                    $valueFound = false;
                    $nextElementRequested = false;
                    break;
                case ':':
                    if (false === $keyFound) {
                        InvalidJsonException::trigger('Invalid character ":", \'"\' expected', $dataSource);
                    }
                    if (true === $valueFound) {
                        InvalidJsonException::trigger('Unexpected object value. Key or end of object expected',
                            $dataSource);
                    }
                    $currentValue->setValue(States::$VALUE->run($dataSource));
                    $values->addValue($currentValue);
                    $keyFound = false;
                    $valueFound = true;
                    $nextElementRequested = false;
                    break;
                case ',':
                    if (true === $keyFound) {
                        InvalidJsonException::trigger('Invalid character ",", expected value', $dataSource);
                    }
                    $currentValue = new ObjectElementValue();
                    $values->addValue($currentValue);
                    $nextElementRequested = true;
                    break;
                default:
                    InvalidJsonException::trigger(
                        sprintf('Invalid character "%s", expected one of ["{", "["]', $char),
                        $dataSource);
            }
        }

        InvalidJsonException::trigger('Unexpected end of data, end of object expected', $dataSource);
    }
}
