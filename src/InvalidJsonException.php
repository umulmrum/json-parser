<?php

namespace Umulmrum\JsonParser;

/**
 * Thrown on errors in the JSON string to parse.
 */
class InvalidJsonException extends \Exception
{
    /**
     * @var int
     */
    private $jsonLine;
    /**
     * @var int
     */
    private $jsonCol;

    public function __construct(string $message, int $jsonLine, int $jsonCol)
    {
        $message = sprintf('%s in line %d, col %d', $message, $jsonLine, $jsonCol);
        parent::__construct($message);
        $this->jsonLine = $jsonLine;
        $this->jsonCol = $jsonCol;
    }

    public function getJsonLine(): int
    {
        return $this->jsonLine;
    }

    public function getJsonCol(): int
    {
        return $this->jsonCol;
    }
}
