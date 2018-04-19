<?php

namespace umulmrum\JsonParser\State;

class FalseState extends AbstractKeywordState
{
    /**
     * {@inheritdoc}
     */
    protected function getWord(): string
    {
        return 'false';
    }

    /**
     * {@inheritdoc}
     */
    protected function getValue()
    {
        return false;
    }
}
