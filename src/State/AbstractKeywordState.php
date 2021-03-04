<?php

namespace umulmrum\JsonParser\State;

use umulmrum\JsonParser\DataSource\DataSourceInterface;
use umulmrum\JsonParser\InvalidJsonException;

/**
 * @internal
 */
abstract class AbstractKeywordState implements StateInterface
{
    /**
     * {@inheritdoc}
     */
    public function run(DataSourceInterface $dataSource)
    {
        $position = 1;
        $word = $this->getWord();
        $wordLength = \mb_strlen($word);
        while (null !== $char = $dataSource->read()) {
            if ($word[$position] !== $char) {
                throw new InvalidJsonException(\sprintf('"%s" expected, got something else', $this->getWord()), $dataSource->getCurrentLine(), $dataSource->getCurrentCol());
            }
            if ($position === $wordLength - 1) {
                return $this->getValue();
            }
            ++$position;
        }

        throw new InvalidJsonException(\sprintf('Unexpected end of data, "%s" value expected', $this->getWord()), $dataSource->getCurrentLine(), $dataSource->getCurrentCol());
    }

    /**
     * Returns the keyword to match.
     */
    abstract protected function getWord(): string;

    /**
     * Returns the value to return if the keyword was successfully matched.
     *
     * @return mixed
     */
    abstract protected function getValue();
}
