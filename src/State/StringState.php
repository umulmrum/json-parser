<?php

namespace umulmrum\JsonParser\State;

use umulmrum\JsonParser\DataSource\DataSourceInterface;
use umulmrum\JsonParser\InvalidJsonException;

class StringState implements StateInterface
{
    /**
     * {@inheritdoc}
     */
    public function run(DataSourceInterface $dataSource)
    {
        $result = '';
        while (null !== $char = $dataSource->read()) {
            switch ($char) {
                case '\\':
                    $result .= States::$ESCAPED_STRING->run($dataSource);
                    break;
                case '"':
                    return $result;
                default:
                    $result .= $char;
            }
        }

        InvalidJsonException::trigger('Unexpected end of data, string termination expected', $dataSource);
    }
}
