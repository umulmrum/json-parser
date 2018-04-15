<?php

namespace umulmrum\JsonParser\State;

use umulmrum\JsonParser\Value\TrueValue;
use umulmrum\JsonParser\Value\ValueInterface;

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
    protected function getValue(): ValueInterface
    {
        return TrueValue::getInstance();
    }
}
