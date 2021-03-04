<?php

namespace umulmrum\JsonParser;

use umulmrum\JsonParser\DataSource\DataSourceException;
use umulmrum\JsonParser\DataSource\DataSourceInterface;
use umulmrum\JsonParser\DataSource\FileDataSource;
use umulmrum\JsonParser\DataSource\StringDataSource;
use umulmrum\JsonParser\State\StateInterface;
use umulmrum\JsonParser\State\States;
use umulmrum\JsonParser\State\WhitespaceTrait;

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
     * Returns the complete content of the JSON string provided by the underlying data source (equivalent to
     * \json_decode()).
     *
     * @throws DataSourceException
     * @throws InvalidJsonException
     */
    public function all(): ?array
    {
        $result = [];
        $hasResult = false;
        foreach ($this->generate() as $value) {
            if (null !== $value) {
                $key = \key($value);
                if (null === $key) {
                    return [];
                }
                $hasResult = true;
                $result[$key] = \current($value);
            }
        }
        if (false === $hasResult) {
            return null;
        }

        return $result;
    }

    /**
     * Returns a \Generator that generates single elements from the underlying data source.
     * The returned elements are the first-level elements of the root array/object.
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
                $value = $state->run($this->dataSource);
                $state = $this->getNextState($state);
                if (null === $value) {
                    continue;
                }

                $key = \key($value);
                if (null === $key) {
                    yield [];
                }
                if (0 === $key) {
                    $value = [
                        $index => $value[0],
                    ];
                }
                ++$index;

                yield $value;
            }
        } finally {
            $this->dataSource->finish();
        }
    }

    /**
     * @throws DataSourceException
     * @throws InvalidJsonException
     */
    private function getNextState(StateInterface $previousState): StateInterface
    {
        while (null !== $char = $this->dataSource->read()) {
            if ($this->isWhitespace($char)) {
                continue;
            }
            switch ($char) {
                case ',':
                    if (States::$DOCUMENT_START === $previousState) {
                        throw new InvalidJsonException('Unexpected character ",", expected one of "[", "{"', $this->dataSource->getCurrentLine(), $this->dataSource->getCurrentCol());
                    }

                    return $previousState;
                case '[':
                    return States::$ROOT_ARRAY;
                case '{':
                    return States::$ROOT_OBJECT;
                case ']':
                case '}':
                    return States::$DOCUMENT_END;
                default:
                    if (States::$DOCUMENT_START === $previousState) {
                        $message = sprintf('Unexpected character "%s", expected one of "[", "{"', $char);
                    } else {
                        $message = sprintf('Unexpected character "%s", expected one of ",", "[", "{"', $char);
                    }
                    throw new InvalidJsonException($message, $this->dataSource->getCurrentLine(), $this->dataSource->getCurrentCol());
            }
        }

        return States::$DOCUMENT_END;
    }

    public static function fromString(string $data): JsonParser
    {
        return new JsonParser(new StringDataSource($data));
    }

    /**
     * @throws DataSourceException
     */
    public static function fromFile(string $filePath): JsonParser
    {
        return new JsonParser(new FileDataSource($filePath));
    }
}
