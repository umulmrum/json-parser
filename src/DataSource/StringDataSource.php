<?php

namespace Umulmrum\JsonParser\DataSource;

class StringDataSource extends AbstractDataSource
{
    /**
     * @var string|null
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
     * @var bool
     */
    private $lastCharWasLineFeed = false;

    public function __construct(string $data)
    {
        $this->data = $data;
        $this->length = \mb_strlen($data);
    }

    /**
     * {@inheritdoc}
     */
    public function read(): ?string
    {
        if (null === $this->data) {
            throw new DataSourceException('Data source is already finished, cannot read.');
        }
        if ($this->position >= $this->length) {
            return null;
        }
        $char = mb_substr($this->data, $this->position, 1);
        ++$this->position;
        if (true === $this->lastCharWasLineFeed) {
            ++$this->line;
            $this->col = 1;
            $this->lastCharWasLineFeed = false;
        } else {
            ++$this->col;
        }

        if ("\n" === $char) {
            $this->lastCharWasLineFeed = true;
        }

        return $char;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind(): void
    {
        --$this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function finish(): void
    {
        $this->data = null;
    }
}
