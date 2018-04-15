<?php

namespace umulmrum\JsonParser\State;

use umulmrum\JsonParser\DataSource\DataSourceInterface;
use umulmrum\JsonParser\InvalidJsonException;
use umulmrum\JsonParser\Value\StringValue;
use umulmrum\JsonParser\Value\ValueInterface;

class StringState implements StateInterface
{
    /**
     * {@inheritdoc}
     */
    public function run(DataSourceInterface $dataSource): ?ValueInterface
    {
        $isEscaped = false;
        $result = '';
        while (null !== $char = $dataSource->read()) {
            switch ($char) {
                case '"':
                    if (true === $isEscaped) {
                        $result .= $char;
                        $isEscaped = false;
                    } else {
                        return new StringValue($result);
                    }
                    break;
                case '\\':
                    if (true === $isEscaped) {
                        $result .= '\\';
                        $isEscaped = false;
                    } else {
                        $isEscaped = true;
                    }
                    break;
                case 'b':
                    if (true === $isEscaped) {
                        $result .= "\b";
                        $isEscaped = false;
                    } else {
                        $result .= 'b';
                    }
                    break;
                case 'f':
                    if (true === $isEscaped) {
                        $result .= "\f";
                        $isEscaped = false;
                    } else {
                        $result .= 'f';
                    }
                    break;
                case 'n':
                    if (true === $isEscaped) {
                        $result .= "\n";
                        $isEscaped = false;
                    } else {
                        $result .= 'n';
                    }
                    break;
                case 'r':
                    if (true === $isEscaped) {
                        $result .= "\r";
                        $isEscaped = false;
                    } else {
                        $result .= 'r';
                    }
                    break;
                case 't':
                    if (true === $isEscaped) {
                        $result .= "\t";
                        $isEscaped = false;
                    } else {
                        $result .= 't';
                    }
                    break;
                case 'u':
                    if (true === $isEscaped) {
                        $count = 0;
                        $part = '';
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
                            $part .= $char;
                            ++$count;
                        }
                        $result .= \chr(\hexdec($part));
                        $isEscaped = false;
                    } else {
                        $result .= 'u';
                    }
                    break;
                default:
                    $result .= $char;
            }
        }

        InvalidJsonException::trigger('Unexpected end of data, string termination expected', $dataSource);
    }
}
