<?php

namespace umulmrum\JsonParser\State;

use umulmrum\JsonParser\DataSource\DataSourceInterface;
use umulmrum\JsonParser\InvalidJsonException;

class RootObjectState implements StateInterface
{
    use WhitespaceTrait;

    /**
     * {@inheritdoc}
     */
    public function run(DataSourceInterface $dataSource)
    {
        $currentKey = null;

        while (null !== $char = $dataSource->read()) {
            if (true === $this->isWhitespace($char)) {
                continue;
            }
            switch ($char) {
                case '}':
                    if (null === $currentKey) {
                        return [];
                    }

                    throw new InvalidJsonException('No value found for key', $dataSource->getCurrentLine(), $dataSource->getCurrentCol());
                case '"':
                    if (null !== $currentKey) {
                        throw new InvalidJsonException('Invalid character \'"\', ":" expected', $dataSource->getCurrentLine(), $dataSource->getCurrentCol());
                    }
                    $currentKey = States::$STRING->run($dataSource);
                    break;
                case ':':
                    if (null === $currentKey) {
                        throw new InvalidJsonException('Invalid character ":", \'"\' expected', $dataSource->getCurrentLine(), $dataSource->getCurrentCol());
                    }

                    return [
                        $currentKey => States::$VALUE->run($dataSource),
                    ];
                case ',':
                    throw new InvalidJsonException('Invalid character ","', $dataSource->getCurrentLine(), $dataSource->getCurrentCol());
                default:
                    throw new InvalidJsonException(\sprintf('Invalid character "%s", expected one of ["{", "["]', $char), $dataSource->getCurrentLine(), $dataSource->getCurrentCol());
            }
        }

        throw new InvalidJsonException('Unexpected end of data, end of object expected', $dataSource->getCurrentLine(), $dataSource->getCurrentCol());
    }
}
