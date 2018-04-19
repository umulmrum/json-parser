<?php

namespace umulmrum\JsonParser\State;

use umulmrum\JsonParser\DataSource\DataSourceInterface;
use umulmrum\JsonParser\InvalidJsonException;

class ObjectState implements StateInterface
{
    use WhitespaceTrait;

    /**
     * {@inheritdoc}
     */
    public function run(DataSourceInterface $dataSource)
    {
        $values = [];
        $valueFound = false;
        $nextElementRequested = false;
        $currentKey = null;

        while (null !== $char = $dataSource->read()) {
            if (true === $this->isWhitespace($char)) {
                continue;
            }
            switch ($char) {
                case '}':
                    if (null !== $currentKey && false === $valueFound) {
                        InvalidJsonException::trigger('No value found for key', $dataSource);
                    }
                    if (true === $nextElementRequested) {
                        InvalidJsonException::trigger('Unexpected character "}", expected next element', $dataSource);
                    }

                    return $values;
                case '"':
                    if (null !== $currentKey) {
                        InvalidJsonException::trigger('Invalid character \'"\', ":" expected', $dataSource);
                    }
                    $currentKey = States::$STRING->run($dataSource);
                    $valueFound = false;
                    $nextElementRequested = false;
                    break;
                case ':':
                    if (null === $currentKey) {
                        InvalidJsonException::trigger('Invalid character ":", \'"\' expected', $dataSource);
                    }
                    if (true === $valueFound) {
                        InvalidJsonException::trigger('Unexpected object value. Key or end of object expected',
                            $dataSource);
                    }
                    $values[$currentKey] = States::$VALUE->run($dataSource);
                    $currentKey = null;
                    $valueFound = true;
                    $nextElementRequested = false;
                    break;
                case ',':
                    if (null !== $currentKey) {
                        InvalidJsonException::trigger('Invalid character ",", expected value', $dataSource);
                    }
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
