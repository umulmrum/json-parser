<?php

namespace Umulmrum\JsonParser\State;

use Umulmrum\JsonParser\DataSource\DataSourceInterface;
use Umulmrum\JsonParser\InvalidJsonException;

/**
 * @internal
 */
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
                case '"':
                    return $result;
                case '\\':
                    $result .= States::$ESCAPED_STRING->run($dataSource);
                    break;
                default:
                    $result .= $char;
                    break;
            }
        }

        throw new InvalidJsonException('Unexpected end of data, string termination expected', $dataSource->getCurrentLine(), $dataSource->getCurrentCol());
    }
}
