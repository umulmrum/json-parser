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
                        throw new InvalidJsonException('Unexpected character ",", expected value', $dataSource->getCurrentLine(), $dataSource->getCurrentCol());
                    }
                    $isValueExpected = true;
                    $isEndExpected = false;
                    break;
                case ']':
                    if (false === $isEndExpected) {
                        throw new InvalidJsonException('Unexpected character "]", expected value', $dataSource->getCurrentLine(), $dataSource->getCurrentCol());
                    }

                    return $values;
                default:
                    if (false === $isValueExpected) {
                        throw new InvalidJsonException(\sprintf('Unexpected character "%s", expected "," or "]"', $char), $dataSource->getCurrentLine(), $dataSource->getCurrentCol());
                    }
                    $dataSource->rewind();
                    $values[$key] = States::$VALUE->run($dataSource);
                    ++$key;
                    $isValueExpected = false;
                    $isEndExpected = true;
                    break;
            }
        }

        throw new InvalidJsonException('Unexpected end of data, end of array expected', $dataSource->getCurrentLine(), $dataSource->getCurrentCol());
    }
}
