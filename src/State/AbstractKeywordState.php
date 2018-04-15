<?php

namespace umulmrum\JsonParser\State;

use umulmrum\JsonParser\DataSource\DataSourceInterface;
use umulmrum\JsonParser\InvalidJsonException;
use umulmrum\JsonParser\Value\ValueInterface;

abstract class AbstractKeywordState implements StateInterface
{
    /**
     * {@inheritdoc}
     */
    public function run(DataSourceInterface $dataSource): ?ValueInterface
    {
        $position = 0;
        $word = $this->getWord();
        $wordLength = \mb_strlen($word);
        while (null !== $char = $dataSource->read()) {
            if ($word[$position] !== $char) {
                InvalidJsonException::trigger(
                    sprintf('"%s" expected, got something else', $this->getValue()->getValue()), $dataSource);
            }
            if ($position === $wordLength - 1) {
                return $this->getValue();
            }
            ++$position;
        }

        InvalidJsonException::trigger(
            sprintf('Unexpected end of data, "%s" value expected', $this->getValue()->getValue()), $dataSource);
    }

    /**
     * Returns the keyword to match.
     *
     * @return string
     */
    abstract protected function getWord(): string;

    /**
     * Returns the ValueInterface to return if the keyword was successfully matched.
     *
     * @return ValueInterface
     */
    abstract protected function getValue(): ValueInterface;
}
