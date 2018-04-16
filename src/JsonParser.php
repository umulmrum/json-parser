<?php

namespace umulmrum\JsonParser;

use umulmrum\JsonParser\DataSource\DataSourceException;
use umulmrum\JsonParser\DataSource\DataSourceInterface;
use umulmrum\JsonParser\DataSource\FileDataSource;
use umulmrum\JsonParser\DataSource\StringDataSource;
use umulmrum\JsonParser\State\StateInterface;
use umulmrum\JsonParser\State\States;
use umulmrum\JsonParser\State\WhitespaceTrait;
use umulmrum\JsonParser\Value\EmptyValue;
use umulmrum\JsonParser\Value\ObjectListValue;
use umulmrum\JsonParser\Value\ValueInterface;

class JsonParser
{
    use WhitespaceTrait;

    /**
     * @var DataSourceInterface
     */
    private $dataSource;

    public function __construct(DataSourceInterface $dataSource)
    {
        $this->dataSource = $dataSource;
    }

    /**
     * @return array|null
     *
     * @throws DataSourceException
     * @throws InvalidJsonException
     */
    public function all(): ?array
    {
        $result = [];
        $hasResult = false;
        /**
         * @var ValueInterface $value
         */
        foreach ($this->generate() as $value) {
            if (null !== $value) {
                if ($value instanceof EmptyValue) {
                    return [];
                }
                $hasResult = true;
                /**
                 * @var ObjectListValue $value
                 */
                $result[$value->getFirstKey()] = $value->getFirstValue()->getValue();
            }
        }
        if (false === $hasResult) {
            return null;
        }

        return $result;
    }

    /**
     * @return \Generator
     *
     * @throws DataSourceException
     * @throws InvalidJsonException
     */
    public function generate(): \Generator
    {
        $state = States::$DOCUMENT_START;
        $index = 0;

        try {
            while (States::$DOCUMENT_END !== $state) {
                /**
                 * @var ObjectListValue $value
                 */
                $value = $state->run($this->dataSource, 0);
                $state = $this->getNextState($state);
                if (null !== $value) {
                    if ($value instanceof ObjectListValue && 0 === $value->getFirstKey()) {
                        $element = $value->getFirstValue();
                        $element->setKey($index);
                        $value = new ObjectListValue();
                        $value->addValue($element);
                    }
                    ++$index;

                    yield $value;
                }
            }
        } finally {
            $this->dataSource->finish();
        }

        return null;
    }

    /**
     * @param StateInterface $previousState
     *
     * @return StateInterface
     *
     * @throws DataSourceException
     * @throws InvalidJsonException
     */
    private function getNextState(StateInterface $previousState)
    {
        $isNextElementRequested = false;
        while (null !== $char = $this->dataSource->read()) {
            if ($this->isWhitespace($char)) {
                continue;
            }
            switch ($char) {
                case ',':
                    if (States::$DOCUMENT_START === $previousState || true === $isNextElementRequested) {
                        InvalidJsonException::trigger('Unexpected character ",", expected one of "[", "{"',
                            $this->dataSource);
                    }

                    return $previousState;
                case '[':
                    if (true === $isNextElementRequested) {
                        if (States::$ROOT_ARRAY === $previousState) {
                            return $previousState;
                        }

                        InvalidJsonException::trigger('Invalid character "["', $this->dataSource);
                    } else {
                        return States::$ROOT_ARRAY;
                    }
                    // no break
                case ']':
                case '}':
                    return States::$DOCUMENT_END;
                case '{':
                    if (true === $isNextElementRequested) {
                        if (States::$ROOT_OBJECT === $previousState) {
                            return $previousState;
                        }

                        InvalidJsonException::trigger('Invalid character "{"', $this->dataSource);
                    } else {
                        return States::$ROOT_OBJECT;
                    }
                    // no break
                default:
                    if (States::$DOCUMENT_START === $previousState) {
                        $message = sprintf('Unexpected character "%s", expected one of "[", "{"', $char);
                    } else {
                        $message = sprintf('Unexpected character "%s", expected one of ",", "[", "{"', $char);
                    }
                    InvalidJsonException::trigger($message, $this->dataSource);
            }
        }

        return States::$DOCUMENT_END;
    }

    /**
     * @param string $data
     *
     * @return JsonParser
     */
    public static function fromString(string $data): JsonParser
    {
        return new JsonParser(new StringDataSource($data));
    }

    /**
     * @param string $filePath
     *
     * @return JsonParser
     *
     * @throws DataSourceException
     */
    public static function fromFile(string $filePath): JsonParser
    {
        return new JsonParser(new FileDataSource($filePath));
    }
}
