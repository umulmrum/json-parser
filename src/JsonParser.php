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
use umulmrum\JsonParser\Value\ObjectValue;
use umulmrum\JsonParser\Value\ObjectValueList;
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
                 * @var ObjectValue $value
                 */
                if ($value instanceof ObjectValue) {
                    $result[$value->getKey()] = $value->getValue();
                } else {
                    $result[] = $value->getValue();
                }
//                $result = array_merge($result, $value->getValue());
            }
        }
        if (false === $hasResult) {
            return null;
        }

        return $result;
    }

    /**
     * @return \Generator
     * @throws InvalidJsonException
     */
    public function generate(): \Generator
    {
        $state = States::$DOCUMENT_START;

        try {
            while (States::$DOCUMENT_END !== $state) {
                $value = $state->run($this->dataSource);
                $state = $this->getNextState($state);
                if (null !== $value) {
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
     * @return StateInterface
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
//                    $isNextElementRequested = true;
//                    break;
                case '[':
                    if (true === $isNextElementRequested) {
                        if (States::$ROOT_ARRAY === $previousState) {
                            return $previousState;
                        } else {
                            InvalidJsonException::trigger('Invalid character "["', $this->dataSource);
                        }
                    } else {
                        return States::$ROOT_ARRAY;
                    }
                case ']':
                case '}':
                    return States::$DOCUMENT_END;
                case '{':
                    if (true === $isNextElementRequested) {
                        if (States::$ROOT_OBJECT === $previousState) {
                            return $previousState;
                        } else {
                            InvalidJsonException::trigger('Invalid character "{"', $this->dataSource);
                        }
                    } else {
                        return States::$ROOT_OBJECT;
                    }
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

    public static function fromString(string $data): JsonParser
    {
        return new JsonParser(new StringDataSource($data));
    }

    /**
     * @param string $filePath
     * @return JsonParser
     *
     * @throws DataSourceException
     */
    public static function fromFile(string $filePath): JsonParser
    {
        return new JsonParser(new FileDataSource($filePath));
    }
}