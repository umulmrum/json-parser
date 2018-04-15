<?php

namespace umulmrum\JsonParser\State;

use umulmrum\JsonParser\DataSource\DataSourceInterface;
use umulmrum\JsonParser\InvalidJsonException;
use umulmrum\JsonParser\Value\EmptyValue;
use umulmrum\JsonParser\Value\ObjectValue;
use umulmrum\JsonParser\Value\ValueInterface;

class RootObjectState implements StateInterface
{
    use WhitespaceTrait;

    /**
     * {@inheritdoc}
     */
    public function run(DataSourceInterface $dataSource): ?ValueInterface
    {
        $keyFound = false;
        $valueFound = false;
        $value = new ObjectValue();

        while (null !== $char = $dataSource->read()) {
            if (true === $this->isWhitespace($char)) {
                continue;
            }
            switch ($char) {
                case '}':
                    if (false === $keyFound && false === $valueFound) {
                        return EmptyValue::getInstance();
                    }
                    if (true === $keyFound && false === $valueFound) {
                        InvalidJsonException::trigger('No value found for key', $dataSource);
                    }

                    return $value;
                case '"':
                    if (true === $keyFound) {
                        InvalidJsonException::trigger('Invalid character \'"\', ":" expected', $dataSource);
                    }
                    $value->setKey(States::$STRING->run($dataSource)->getValue());
                    $keyFound = true;
                    $valueFound = false;
                    break;
                case ':':
                    if (false === $keyFound) {
                        InvalidJsonException::trigger('Invalid character ":", \'"\' expected', $dataSource);
                    }
                    if (true === $valueFound) {
                        InvalidJsonException::trigger('Unexpected object value. Key or end of object expected',
                            $dataSource);
                    }
                    $value->setValue(States::$VALUE->run($dataSource));

                    return $value;
                case ',':
                    InvalidJsonException::trigger('Invalid character ","', $dataSource);
                    // no break
                default:
                    InvalidJsonException::trigger(
                        sprintf('Invalid character "%s", expected one of ["{", "["]', $char),
                        $dataSource);
            }
        }

        InvalidJsonException::trigger('Unexpected end of data, end of object expected', $dataSource);
    }
}
