<?php

namespace umulmrum\JsonParser\State;

use umulmrum\JsonParser\DataSource\DataSourceInterface;
use umulmrum\JsonParser\InvalidJsonException;

class ArrayState implements StateInterface
{
    use WhitespaceTrait;

    /**
     * {@inheritdoc}
     */
    public function run(DataSourceInterface $dataSource)
    {
        $values = [];
        $key = 0;
        $isValueExpected = true;
        $isEndExpected = true;
        while (null !== $char = $dataSource->read()) {
            if (true === $this->isWhitespace($char)) {
                continue;
            }
            switch ($char) {
                case ',':
                    if (true === $isValueExpected) {
                        InvalidJsonException::trigger('Unexpected character ",", expected value', $dataSource);
                    }
                    $isValueExpected = true;
                    $isEndExpected = false;
                    break;
                case ']':
                    if (false === $isEndExpected) {
                        InvalidJsonException::trigger('Unexpected character "]", expected value', $dataSource);
                    }

                    return $values;
                default:
                    if (false === $isValueExpected) {
                        InvalidJsonException::trigger(sprintf('Unexpexted character "%s", expected "," or "]"', $char), $dataSource);
                    }
                    $dataSource->rewind();
                    $values[$key] = States::$VALUE->run($dataSource);
                    ++$key;
                    $isValueExpected = false;
                    $isEndExpected = true;
                    break;
            }
        }

        InvalidJsonException::trigger('Unexpected end of data, end of array expected', $dataSource);
    }
}
