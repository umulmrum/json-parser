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
            /**
             * @var $state StateInterface
             */
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
                        $dataSource->rewind();
                        $state = States::$TRUE;
                        break;
                    case 'f':
                        $dataSource->rewind();
                        $state = States::$FALSE;
                        break;
                    case 'n':
                        $dataSource->rewind();
                        $state = States::$NULL;
                        break;
                    default:
                        InvalidJsonException::trigger(sprintf('Value expected, found "%s"', $char), $dataSource);
                }
            }

            return $state->run($dataSource);
        }

        InvalidJsonException::trigger('Unexpected end of data, value expected', $dataSource);
    }
}
