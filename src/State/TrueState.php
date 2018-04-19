<?php

namespace umulmrum\JsonParser\State;

class TrueState extends AbstractKeywordState
{
    /**
     * {@inheritdoc}
     */
    protected function getWord(): string
    {
        return 'true';
    }

    /**
     * {@inheritdoc}
     */
    protected function getValue()
    {
        return true;
    }
}
