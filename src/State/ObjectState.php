<?php

namespace Umulmrum\JsonParser\State;

use Umulmrum\JsonParser\DataSource\DataSourceInterface;
use Umulmrum\JsonParser\InvalidJsonException;

/**
 * @internal
 */
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
                        throw new InvalidJsonException('No value found for key', $dataSource->getCurrentLine(), $dataSource->getCurrentCol());
                    }
                    if (true === $nextElementRequested) {
                        throw new InvalidJsonException('Unexpected character "}", expected next element', $dataSource->getCurrentLine(), $dataSource->getCurrentCol());
                    }

                    return $values;
                case '"':
                    if (null !== $currentKey) {
                        throw new InvalidJsonException('Invalid character \'"\', ":" expected', $dataSource->getCurrentLine(), $dataSource->getCurrentCol());
                    }
                    $currentKey = States::$STRING->run($dataSource);
                    $valueFound = false;
                    $nextElementRequested = false;
                    break;
                case ':':
                    if (null === $currentKey) {
                        throw new InvalidJsonException('Invalid character ":", \'"\' expected', $dataSource->getCurrentLine(), $dataSource->getCurrentCol());
                    }
                    $values[$currentKey] = States::$VALUE->run($dataSource);
                    $currentKey = null;
                    $valueFound = true;
                    $nextElementRequested = false;
                    break;
                case ',':
                    if (null !== $currentKey) {
                        throw new InvalidJsonException('Invalid character ",", expected value', $dataSource->getCurrentLine(), $dataSource->getCurrentCol());
                    }
                    $nextElementRequested = true;
                    break;
                default:
                    throw new InvalidJsonException(\sprintf('Invalid character "%s", expected one of ["{", "["]', $char), $dataSource->getCurrentLine(), $dataSource->getCurrentCol());
            }
        }

        throw new InvalidJsonException('Unexpected end of data, end of object expected', $dataSource->getCurrentLine(), $dataSource->getCurrentCol());
    }
}
