<?php

namespace Umulmrum\JsonParser\State;

/**
 * @internal
 */
class NullState extends AbstractKeywordState
{
    /**
     * {@inheritdoc}
     */
    protected function getWord(): string
    {
        return 'null';
    }

    /**
     * {@inheritdoc}
     */
    protected function getValue()
    {
        return null;
    }
}
