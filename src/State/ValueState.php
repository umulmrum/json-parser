<?php

namespace umulmrum\JsonParser\State;

use umulmrum\JsonParser\DataSource\DataSourceInterface;
use umulmrum\JsonParser\InvalidJsonException;

class ValueState implements StateInterface
{
    use WhitespaceTrait;

    /**
     * {@inheritdoc}
     */
    public function run(DataSourceInterface $dataSource)
    {
        while (null !== $char = $dataSource->read()) {
            if ($this->isWhitespace($char)) {
                continue;
            }
            $state = null;
            if (true === is_numeric($char) || '-' === $char) {
                $dataSource->rewind();
                $state = States::$NUMERIC;
            } else {
                switch ($char) {
                    case '"':
                        $state = States::$STRING;
                        break;
                    case '{':
                        $state = States::$OBJECT;
                        break;
                    case '[':
                        $state = States::$ARRAY;
                        break;
                    case 't':
                        $state = States::$TRUE;
                        break;
                    case 'f':
                        $state = States::$FALSE;
                        break;
                    case 'n':
                        $state = States::$NULL;
                        break;
                    default:
                        throw new InvalidJsonException(\sprintf('Value expected, found "%s"', $char), $dataSource->getCurrentLine(), $dataSource->getCurrentCol());
                }
            }

            return $state->run($dataSource);
        }

        throw new InvalidJsonException('Unexpected end of data, value expected', $dataSource->getCurrentLine(), $dataSource->getCurrentCol());
    }
}
