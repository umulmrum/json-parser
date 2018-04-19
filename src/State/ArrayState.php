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
                    $values[$key] = States::$VALUE->run($dataSource);
                    ++$key;
                    break;
            }
        }

        InvalidJsonException::trigger('Unexpected end of data, end of array expected', $dataSource);
    }
}
