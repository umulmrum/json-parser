<?php

namespace umulmrum\JsonParser\State;

use umulmrum\JsonParser\DataSource\DataSourceInterface;
use umulmrum\JsonParser\InvalidJsonException;

/**
 * @internal
 */
class NumericState implements StateInterface
{
    use WhitespaceTrait;

    /**
     * {@inheritdoc}
     */
    public function run(DataSourceInterface $dataSource)
    {
        $number = '';
        $isFloat = false;
        $hasE = false;
        $lastCharWasE = false;
        $lastCharWasLeadingZero = false;
        while (null !== $char = $dataSource->read()) {
            if (true === $this->isWhitespace($char)) {
                $lastCharWasE = false;
                while (null !== $char = $dataSource->read()) {
                    if (true === $this->isWhitespace($char)) {
                        continue;
                    }
                    switch ($char) {
                        case ',':
                        case ']':
                        case '}':
                            $dataSource->rewind();

                            return $this->getNumber($number, $isFloat);
                        default:
                            throw new InvalidJsonException(\sprintf('Unexpected character %s, expected end of number', $char), $dataSource->getCurrentLine(), $dataSource->getCurrentCol());
                    }
                }
            }

            switch ($char) {
                case '.':
                    $lastCharWasE = false;
                    if ('' === $number) {
                        throw new InvalidJsonException('Unexpected character ".", expected digit or "-"', $dataSource->getCurrentLine(), $dataSource->getCurrentCol());
                    } elseif (true === $isFloat) {
                        throw new InvalidJsonException('Unexpected character ".", expected digit or end of number', $dataSource->getCurrentLine(), $dataSource->getCurrentCol());
                    } else {
                        $isFloat = true;
                        $number .= $char;
                        $lastCharWasLeadingZero = false;
                    }
                    break;
                case '0':
                    if ('' === $number) {
                        $lastCharWasLeadingZero = true;
                    }
                    $lastCharWasE = false;
                    $number .= $char;
                    break;
                case '1':
                case '2':
                case '3':
                case '4':
                case '5':
                case '6':
                case '7':
                case '8':
                case '9':
                    if (true === $lastCharWasLeadingZero) {
                        throw new InvalidJsonException(\sprintf('Unexpected character %s, expected dot, E, or end of number', $char), $dataSource->getCurrentLine(), $dataSource->getCurrentCol());
                    }
                    $lastCharWasE = false;
                    $number .= $char;
                    break;
                case ',':
                case ']':
                case '}':
                    $dataSource->rewind();

                    return $this->getNumber($number, $isFloat);
                case 'e':
                case 'E':
                    $lastCharWasLeadingZero = false;
                    if (true === $hasE) {
                        throw new InvalidJsonException(\sprintf('Unexpected character "%s", expected digit or end of number', $char), $dataSource->getCurrentLine(), $dataSource->getCurrentCol());
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
                        throw new InvalidJsonException('Unexpected character "-", expected digit, dot or end of number', $dataSource->getCurrentLine(), $dataSource->getCurrentCol());
                    }
                    $lastCharWasE = false;
                    break;
                case '+':
                    if (false === $lastCharWasE) {
                        throw new InvalidJsonException(\sprintf('Unexpected character "%s", expected digit or end of number', $char), $dataSource->getCurrentLine(), $dataSource->getCurrentCol());
                    }
                    $lastCharWasE = false;
                    $number .= $char;
                    break;
                case null:
                    throw new InvalidJsonException('Unexpected character null, expected digit or "-"', $dataSource->getCurrentLine(), $dataSource->getCurrentCol());
                default:
                    throw new InvalidJsonException(\sprintf('Unexpected character %s, expected digit or "-"', $char), $dataSource->getCurrentLine(), $dataSource->getCurrentCol());
            }
        }

        throw new InvalidJsonException('Unexpected end of data, number termination expected', $dataSource->getCurrentLine(), $dataSource->getCurrentCol());
    }

    /**
     * @param mixed $value
     *
     * @return float|int
     */
    private function getNumber($value, bool $isFloat)
    {
        if (true === $isFloat) {
            return (float) $value;
        }

        return (int) $value;
    }
}
