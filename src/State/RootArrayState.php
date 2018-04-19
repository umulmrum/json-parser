<?php

namespace umulmrum\JsonParser\State;

use umulmrum\JsonParser\DataSource\DataSourceInterface;
use umulmrum\JsonParser\InvalidJsonException;

class RootArrayState implements StateInterface
{
    use WhitespaceTrait;

    /**
     * {@inheritdoc}
     */
    public function run(DataSourceInterface $dataSource)
    {
        while (null !== $char = $dataSource->read()) {
            if (true === $this->isWhitespace($char)) {
                continue;
            }
            if (']' === $char) {
                return [];
            }
            $dataSource->rewind();

            return [ States::$VALUE->run($dataSource) ];
        }

        InvalidJsonException::trigger('Unexpected end of data, end of array expected', $dataSource);
    }
}
