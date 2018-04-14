<?php


namespace umulmrum\JsonParser\DataSource;


class StringDataSource implements DataSourceInterface
{
    /**
     * @var string
     */
    private $data;
    /**
     * @var int
     */
    private $length;
    /**
     * @var int
     */
    private $position = 0;
    /**
     * @var int
     */
    private $line = 1;
    /**
     * @var int
     */
    private $col = 1;
    /**
     * @var bool
     */
    private $lastCharWasLineFeed = false;
    /**
     * @var bool
     */
    private $wasEmpty = true;

    public function __construct(string $data)
    {
        $this->data = $data;
        $this->length = \mb_strlen($data);
    }

    public function read(): ?string
    {
        if ($this->position >= $this->length) {
            return null;
        }
        $char = mb_substr($this->data, $this->position, 1);
        $this->position++;
        if (true === $this->lastCharWasLineFeed) {
            $this->line++;
            $this->col = 1;
            $this->lastCharWasLineFeed = false;
        } else {
            $this->col++;
        }

        if ("\n" === $char) {
            $this->lastCharWasLineFeed = true;
        } else {
            $this->wasEmpty = false;
        }

        return $char;
    }

    public function getCurrentLine(): int
    {
        return $this->line;
    }

    public function getCurrentCol(): int
    {
        return $this->col;
    }

    public function wasEmpty(): bool
    {
        return $this->wasEmpty;
    }

    public function rewind(): void
    {
        $this->position--;
    }
}