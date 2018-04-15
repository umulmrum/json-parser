<?php


namespace umulmrum\JsonParser;



use umulmrum\JsonParser\DataSource\DataSourceInterface;

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

    /**
     * @return int
     */
    public function getJsonLine(): int
    {
        return $this->jsonLine;
    }

    /**
     * @return int
     */
    public function getJsonCol(): int
    {
        return $this->jsonCol;
    }

    /**
     * Convenience method to throw an InvalidJsonException
     *
     * @param string $message
     * @param DataSourceInterface $dataSource
     * @throws InvalidJsonException
     */
    public static function trigger(string $message, DataSourceInterface $dataSource): void
    {
        throw new self($message, $dataSource->getCurrentLine(), $dataSource->getCurrentCol());
    }
}