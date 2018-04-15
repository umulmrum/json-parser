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

    public function getCurrentLine(): int
    {
        return $this->line;
    }

    public function getCurrentCol(): int
    {
        return $this->col;
    }
}