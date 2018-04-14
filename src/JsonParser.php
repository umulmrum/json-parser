<?php


namespace umulmrum\JsonParser;


use umulmrum\JsonParser\DataSource\DataSourceInterface;
use umulmrum\JsonParser\State\StateInterface;
use umulmrum\JsonParser\State\States;
use umulmrum\JsonParser\State\WhitespaceTrait;
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
                $hasResult = true;
                $result = array_merge($result, $value);
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

        while (States::$DOCUMENT_END !== $state) {
            $value = $state->run($this->dataSource);
            $state = $this->getNextState($state);
            if (null !== $value) {
                yield $value->getValue();
            }
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
        while (null !== $char = $this->dataSource->read()) {
            if ($this->isWhitespace($char)) {
                continue;
            }
            switch ($char) {
                case ',':
                    if (States::$DOCUMENT_START === $previousState) {
                        InvalidJsonException::trigger('Unexpected character ",", expected one of ",", "[", "{"',
                            $this->dataSource);
                    }

                    return $previousState;
                case '[':
                    return States::$ARRAY;
                case '{':
                    return States::$OBJECT;
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
}