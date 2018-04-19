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
        $valueFound = false;
        $value = [];

        while (null !== $char = $dataSource->read()) {
            if (true === $this->isWhitespace($char)) {
                continue;
            }
            switch ($char) {
                case '}':
                    if (null === $currentKey && false === $valueFound) {
                        return [];
                    }
                    if (null !== $currentKey && false === $valueFound) {
                        InvalidJsonException::trigger('No value found for key', $dataSource);
                    }

                    return $value;
                case '"':
                    if (null !== $currentKey) {
                        InvalidJsonException::trigger('Invalid character \'"\', ":" expected', $dataSource);
                    }
                    $currentKey = States::$STRING->run($dataSource);
                    $valueFound = false;
                    break;
                case ':':
                    if (null === $currentKey) {
                        InvalidJsonException::trigger('Invalid character ":", \'"\' expected', $dataSource);
                    }
                    if (true === $valueFound) {
                        InvalidJsonException::trigger('Unexpected object value. Key or end of object expected',
                            $dataSource);
                    }

                    return [
                        $currentKey => States::$VALUE->run($dataSource),
                    ];
                case ',':
                    InvalidJsonException::trigger('Invalid character ","', $dataSource);
                default:
                    InvalidJsonException::trigger(
                        sprintf('Invalid character "%s", expected one of ["{", "["]', $char),
                        $dataSource);
            }
        }

        InvalidJsonException::trigger('Unexpected end of data, end of object expected', $dataSource);
    }
}
