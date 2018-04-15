<?php


namespace umulmrum\JsonParser\DataSource;


abstract class AbstractDataSource implements DataSourceInterface
{
    /**
     * @var int
     */
    protected $line = 1;
    /**
     * @var int
     */
    protected $col = 1;

    /**
     * {@inheritDoc}
     */
    public function getCurrentLine(): int
    {
        return $this->line;
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrentCol(): int
    {
        return $this->col;
    }
}