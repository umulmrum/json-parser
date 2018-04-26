<?php

namespace umulmrum\JsonParser\State;

use umulmrum\JsonParser\DataSource\DataSourceException;
use umulmrum\JsonParser\DataSource\DataSourceInterface;
use umulmrum\JsonParser\InvalidJsonException;

class EscapedStringState implements StateInterface
{
    /**
     * {@inheritdoc}
     */
    public function run(DataSourceInterface $dataSource)
    {
        $char = $dataSource->read();
        if (null === $char) {
            InvalidJsonException::trigger('Unexpected end of data, character expected', $dataSource);
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
                InvalidJsonException::trigger(
                    sprintf('Unexpected character "%s, escaped character sequence expected', $char), $dataSource);
        }
    }

    /**
     * Returns a unicode character.
     * See also https://unicodebook.readthedocs.io/unicode_encodings.html#surrogates.
     *
     * @param DataSourceInterface $dataSource
     *
     * @return string
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
            InvalidJsonException::trigger(sprintf('Unexpected character "%s", second part of UTF-16 surrogate pair expected', $char), $dataSource);
        }
        if ('u' !== $char = $dataSource->read()) {
            InvalidJsonException::trigger(sprintf('Unexpected character "%s", second part of UTF-16 surrogate pair expected', $char), $dataSource);
        }

        $partTwo = $this->getSingleUnicodePart($dataSource);
        if ($partTwo < 0xDC00 || $partTwo > 0xDFFF) {
            InvalidJsonException::trigger('Second part of UTF-16 surrogate pair expected, got something else', $dataSource);
        }

        $result = 0x10000;
        $result += ($partOne & 0x03FF) << 10;
        $result += ($partTwo & 0x03FF);

        return \mb_chr($result, 'UTF-8');
    }

    /**
     * @param DataSourceInterface $dataSource
     *
     * @return int
     *
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
                InvalidJsonException::trigger('Unexpected end of data, number expected', $dataSource);
            }
            $charCode = \ord(\mb_strtoupper($char));
            if (($charCode < 48 || $charCode > 57)
                && ($charCode < 65 || $charCode > 90)) {
                InvalidJsonException::trigger(
                    sprintf('Unexpected character "%s", hexadecimal number expected', $char), $dataSource);
            }
            $result .= $char;
            ++$count;
        }

        return \hexdec($result);
    }
}
