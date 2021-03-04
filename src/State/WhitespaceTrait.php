<?php

namespace Umulmrum\JsonParser\State;

/**
 * @internal
 */
trait WhitespaceTrait
{
    private function isWhitespace(string $char): bool
    {
        switch ($char) {
            case ' ':
            case "\n":
            case "\r":
            case "\t":
                return true;
            default:
                return false;
        }
    }
}
