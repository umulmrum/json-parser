<?php

namespace umulmrum\JsonParser\State;

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
