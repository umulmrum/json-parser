<?php

namespace umulmrum\JsonParser\Value;

class NumericValue implements ValueInterface
{
    private $value;
    /**
     * @var bool
     */
    private $isFloat;

    public function __construct($value, bool $isFloat)
    {
        $this->value = $value;
        $this->isFloat = $isFloat;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        if (true === $this->isFloat) {
            return (float) $this->value;
        } else {
            return (int) $this->value;
        }
    }
}
