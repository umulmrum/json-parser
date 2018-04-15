<?php


namespace umulmrum\JsonParser\State;


use umulmrum\JsonParser\Value\NullValue;
use umulmrum\JsonParser\Value\ValueInterface;

class NullState extends AbstractKeywordState
{
    /**
     * {@inheritDoc}
     */
    protected function getWord(): string
    {
        return 'null';
    }

    /**
     * {@inheritDoc}
     */
    protected function getValue(): ValueInterface
    {
        return NullValue::getInstance();
    }
}