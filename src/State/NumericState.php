<?php


namespace umulmrum\JsonParser\State;


use umulmrum\JsonParser\DataSource\DataSourceInterface;
use umulmrum\JsonParser\InvalidJsonException;
use umulmrum\JsonParser\Value\NumericValue;
use umulmrum\JsonParser\Value\ValueInterface;

class NumericState implements StateInterface
{
    use WhitespaceTrait;

    /**
     * {@inheritDoc}
     */
    public function run(DataSourceInterface $dataSource): ?ValueInterface
    {
        $number = '';
        $isFloat = false;
        $hasE = false;
        $lastCharWasE = false;
        while (null !== $char = $dataSource->read()) {
            if (true === $this->isWhitespace($char)) {
                $lastCharWasE = false;
                if ('' === $number) {
                    InvalidJsonException::trigger(
                        'Unexpected whitespace character, expected digit or "-"', $dataSource);
                }

                while (null !== $char = $dataSource->read()) {
                    if (true === $this->isWhitespace($char)) {
                        continue;
                    }
                    switch ($char) {
                        case ',':
                        case ']':
                        case '}':
                            $dataSource->rewind();

                            return new NumericValue($number, $isFloat);
                        default:
                            InvalidJsonException::trigger(
                                sprintf('Unexpected character %s, expected end of number', $char),
                                $dataSource);
                    }
                }
                continue;
            }

            switch ($char) {
                case '.':
                    $lastCharWasE = false;
                    if ('' === $number) {
                        InvalidJsonException::trigger('Unexpected character ".", expected digit or "-"',
                            $dataSource);
                    } elseif (true === $isFloat) {
                        InvalidJsonException::trigger('Unexpected character ".", expected digit or end of number',
                            $dataSource);
                    } else {
                        $isFloat = true;
                        $number .= $char;
                    }
                    break;
                case '0':
                case '1':
                case '2':
                case '3':
                case '4':
                case '5':
                case '6':
                case '7':
                case '8':
                case '9':
                    $lastCharWasE = false;
                    $number .= $char;
                    break;
                case ',':
                case ']':
                case '}':
                    $dataSource->rewind();

                    return new NumericValue($number, $isFloat);
                case 'e':
                case 'E':
                    if (true === $hasE) {
                        InvalidJsonException::trigger(
                            sprintf('Unexpected character "%s", expected digit or end of number', $char),
                            $dataSource);
                    }
                    $number .= $char;
                    $isFloat = true;
                    $hasE = true;
                    $lastCharWasE = true;
                    break;
                case '-':
                    if ('' === $number || true === $lastCharWasE) {
                        $number .= $char;
                    } else {
                        InvalidJsonException::trigger('Unexpected character "-", expected digit, dot or end of number',
                            $dataSource);
                    }
                    $lastCharWasE = false;
                    break;
                case '+':
                    if (false === $lastCharWasE) {
                        InvalidJsonException::trigger(
                            sprintf('Unexpected character "%s", expected digit or end of number', $char),
                            $dataSource);
                    }
                    $lastCharWasE = false;
                    $number .= $char;
                    break;
                default:
                    InvalidJsonException::trigger(
                        sprintf('Unexpected character %s, expected digit or "-"', $char),
                        $dataSource);
            }
        }

        InvalidJsonException::trigger('Unexpected end of data, number termination expected', $dataSource);
    }
}