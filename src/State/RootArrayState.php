<?php

namespace Umulmrum\JsonParser\State;

use Umulmrum\JsonParser\DataSource\DataSourceInterface;
use Umulmrum\JsonParser\InvalidJsonException;

/**
 * @internal
 */
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

            return [States::$VALUE->run($dataSource)];
        }

        throw new InvalidJsonException('Unexpected end of data, end of array expected', $dataSource->getCurrentLine(), $dataSource->getCurrentCol());
    }
}
