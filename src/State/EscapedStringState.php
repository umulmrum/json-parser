<?php

namespace Umulmrum\JsonParser\State;

use Umulmrum\JsonParser\DataSource\DataSourceException;
use Umulmrum\JsonParser\DataSource\DataSourceInterface;
use Umulmrum\JsonParser\InvalidJsonException;

/**
 * @internal
 */
class EscapedStringState implements StateInterface
{
    /**
     * {@inheritdoc}
     */
    public function run(DataSourceInterface $dataSource)
    {
        $char = $dataSource->read();
        if (null === $char) {
            throw new InvalidJsonException('Unexpected end of data, character expected', $dataSource->getCurrentLine(), $dataSource->getCurrentCol());
        }

        switch ($char) {
            case '"':
            case '\\':
            case '/':
                return $char;
            case 'b':
                return \chr(8);
            case 'f':
                return "\f";
            case 'n':
                return "\n";
            case 'r':
                return "\r";
            case 't':
                return "\t";
            case 'u':
                return $this->getUnicodeChar($dataSource);
            default:
                throw new InvalidJsonException(\sprintf('Unexpected character "%s, escaped character sequence expected', $char), $dataSource->getCurrentLine(), $dataSource->getCurrentCol());
        }
    }

    /**
     * Returns a unicode character.
     * See also https://unicodebook.readthedocs.io/unicode_encodings.html#surrogates.
     *
     * @throws DataSourceException
     * @throws InvalidJsonException
     */
    private function getUnicodeChar(DataSourceInterface $dataSource): string
    {
        $partOne = $this->getSingleUnicodePart($dataSource);
        if ($partOne < 0xD800 || $partOne > 0xDBFF) {
            return \chr($partOne);
        }

        if ('\\' !== $char = $dataSource->read()) {
            throw new InvalidJsonException(\sprintf('Unexpected character "%s", second part of UTF-16 surrogate pair expected', $char ?: 'null'), $dataSource->getCurrentLine(), $dataSource->getCurrentCol());
        }
        if ('u' !== $char = $dataSource->read()) {
            throw new InvalidJsonException(\sprintf('Unexpected character "%s", second part of UTF-16 surrogate pair expected', $char ?: 'null'), $dataSource->getCurrentLine(), $dataSource->getCurrentCol());
        }

        $partTwo = $this->getSingleUnicodePart($dataSource);
        if ($partTwo < 0xDC00 || $partTwo > 0xDFFF) {
            throw new InvalidJsonException('Second part of UTF-16 surrogate pair expected, got something else', $dataSource->getCurrentLine(), $dataSource->getCurrentCol());
        }

        $result = 0x10000;
        $result += ($partOne & 0x03FF) << 10;
        $result += ($partTwo & 0x03FF);

        /** @psalm-suppress UndefinedFunction */
        $resultChar = \mb_chr($result, 'UTF-8');

        if (false === $resultChar) {
            throw new InvalidJsonException('Character could not be converted to unicode character', $dataSource->getCurrentLine(), $dataSource->getCurrentCol());
        }

        return $resultChar;
    }

    /**
     * @throws DataSourceException
     * @throws InvalidJsonException
     */
    private function getSingleUnicodePart(DataSourceInterface $dataSource): int
    {
        $count = 0;
        $result = '';
        while ($count < 4) {
            $char = $dataSource->read();
            if (null === $char) {
                throw new InvalidJsonException('Unexpected end of data, number expected', $dataSource->getCurrentLine(), $dataSource->getCurrentCol());
            }
            $charCode = \ord(\mb_strtoupper($char));
            if (($charCode < 48 || $charCode > 57)
                && ($charCode < 65 || $charCode > 90)) {
                throw new InvalidJsonException(\sprintf('Unexpected character "%s", hexadecimal number expected', $char), $dataSource->getCurrentLine(), $dataSource->getCurrentCol());
            }
            $result .= $char;
            ++$count;
        }

        return \hexdec($result);
    }
}
