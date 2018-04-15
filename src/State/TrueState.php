<?php


namespace umulmrum\JsonParser\State;


use umulmrum\JsonParser\DataSource\DataSourceInterface;
use umulmrum\JsonParser\InvalidJsonException;
use umulmrum\JsonParser\Value\TrueValue;
use umulmrum\JsonParser\Value\ValueInterface;

class TrueState extends AbstractKeywordState
{
    /**
     * {@inheritDoc}
     */
    protected function getWord(): string
    {
        return 'true';
    }

    /**
     * {@inheritDoc}
     */
    protected function getValue(): ValueInterface
    {
        return TrueValue::getInstance();
    }
}