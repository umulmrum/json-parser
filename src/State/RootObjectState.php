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

                    InvalidJsonException::trigger('No value found for key', $dataSource);
                case '"':
                    if (null !== $currentKey) {
                        InvalidJsonException::trigger('Invalid character \'"\', ":" expected', $dataSource);
                    }
                    $currentKey = States::$STRING->run($dataSource);
                    break;
                case ':':
                    if (null === $currentKey) {
                        InvalidJsonException::trigger('Invalid character ":", \'"\' expected', $dataSource);
                    }

                    return [
                        $currentKey => States::$VALUE->run($dataSource),
                    ];
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
