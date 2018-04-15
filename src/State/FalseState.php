<?php

namespace umulmrum\JsonParser\State;

use umulmrum\JsonParser\Value\FalseValue;
use umulmrum\JsonParser\Value\ValueInterface;

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
    protected function getValue(): ValueInterface
    {
        return FalseValue::getInstance();
    }
}
