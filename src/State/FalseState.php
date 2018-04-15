<?php


namespace umulmrum\JsonParser\State;


use umulmrum\JsonParser\Value\FalseValue;
use umulmrum\JsonParser\Value\ValueInterface;

class FalseState extends AbstractKeywordState
{
    /**
     * {@inheritDoc}
     */
    protected function getWord(): string
    {
        return 'false';
    }

    /**
     * {@inheritDoc}
     */
    protected function getValue(): ValueInterface
    {
        return FalseValue::getInstance();
    }
}