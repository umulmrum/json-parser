<?php


namespace umulmrum\JsonParser\State;


use umulmrum\JsonParser\DataSource\DataSourceInterface;
use umulmrum\JsonParser\InvalidJsonException;
use umulmrum\JsonParser\Value\ArrayValue;
use umulmrum\JsonParser\Value\EmptyValue;
use umulmrum\JsonParser\Value\ObjectValue;
use umulmrum\JsonParser\Value\ValueInterface;

class RootArrayState implements StateInterface
{
    use WhitespaceTrait;

    /**
     * {@inheritdoc}
     */
    public function run(DataSourceInterface $dataSource): ?ValueInterface
    {
        while (null !== $char = $dataSource->read()) {
            if (true === $this->isWhitespace($char)) {
                continue;
            }
            if (']' === $char) {
                return EmptyValue::getInstance();
            }
            $dataSource->rewind();

            return States::$VALUE->run($dataSource);
        }

        InvalidJsonException::trigger('Unexpected end of data, end of array expected', $dataSource);
    }
}