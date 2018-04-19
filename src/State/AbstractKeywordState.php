<?php

namespace umulmrum\JsonParser\State;

use umulmrum\JsonParser\DataSource\DataSourceInterface;
use umulmrum\JsonParser\InvalidJsonException;

abstract class AbstractKeywordState implements StateInterface
{
    /**
     * {@inheritdoc}
     */
    public function run(DataSourceInterface $dataSource)
    {
        $position = 0;
        $word = $this->getWord();
        $wordLength = \mb_strlen($word);
        while (null !== $char = $dataSource->read()) {
            if ($word[$position] !== $char) {
                InvalidJsonException::trigger(
                    sprintf('"%s" expected, got something else', $this->getWord()), $dataSource);
            }
            if ($position === $wordLength - 1) {
                return $this->getValue();
            }
            ++$position;
        }

        InvalidJsonException::trigger(
            sprintf('Unexpected end of data, "%s" value expected', $this->getWord()), $dataSource);
    }

    /**
     * Returns the keyword to match.
     *
     * @return string
     */
    abstract protected function getWord(): string;

    /**
     * Returns the value to return if the keyword was successfully matched.
     *
     * @return mixed
     */
    abstract protected function getValue();
}
