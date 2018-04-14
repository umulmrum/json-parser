<?php


namespace umulmrum\JsonParser\State;


use umulmrum\JsonParser\Value\NullValue;
use umulmrum\JsonParser\Value\ValueInterface;

class NullState extends AbstractKeywordState
{
    protected function getWord(): string
    {
        return 'null';
    }

    protected function getValue(): ValueInterface
    {
        return NullValue::getInstance();
    }
}