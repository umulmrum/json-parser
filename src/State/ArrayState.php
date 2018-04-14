<?php


namespace umulmrum\JsonParser\State;


use umulmrum\JsonParser\DataSource\DataSourceInterface;
use umulmrum\JsonParser\InvalidJsonException;
use umulmrum\JsonParser\Value\ArrayValue;
use umulmrum\JsonParser\Value\ArrayValues;
use umulmrum\JsonParser\Value\ValueInterface;

class ArrayState implements StateInterface
{
    use WhitespaceTrait;

    /**
     * {@inheritDoc}
     */
    public function run(DataSourceInterface $dataSource): ?ValueInterface
    {
        $values = new ArrayValues();
        while (null !== $char = $dataSource->read()) {
            if (true === $this->isWhitespace($char)) {
                continue;
            }
            switch ($char) {
                case ',':
                    $currentValue = new ArrayValue();
                    $currentValue->setValue(States::$VALUE->run($dataSource));
                    $values->addValue($currentValue);
                    break;
                case ']':
                    return $values;
                default:
                    $dataSource->rewind();
                    $currentValue = new ArrayValue();
                    $currentValue->setValue(States::$VALUE->run($dataSource));
                    $values->addValue($currentValue);
                    break;
            }
        }

        InvalidJsonException::trigger('Unexpected end of data, end of array expected.', $dataSource);
    }
}