<?php


namespace umulmrum\JsonParser\State;


use umulmrum\JsonParser\Value\FalseValue;
use umulmrum\JsonParser\Value\ValueInterface;

class FalseState extends AbstractKeywordState
{
    protected function getWord(): string
    {
        return 'false';
    }

    protected function getValue(): ValueInterface
    {
        return FalseValue::getInstance();
    }
}