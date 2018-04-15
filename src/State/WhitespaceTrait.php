<?php

namespace umulmrum\JsonParser\State;

trait WhitespaceTrait
{
    private function isWhitespace(string $char): bool
    {
        switch ($char) {
            case "\n":
            case "\r":
            case "\t":
            case ' ':
                return true;
            default:
                return false;
        }
    }
}
