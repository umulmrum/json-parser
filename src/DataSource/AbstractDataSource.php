<?php

namespace Umulmrum\JsonParser\DataSource;

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
     * {@inheritdoc}
     */
    public function getCurrentLine(): int
    {
        return $this->line;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentCol(): int
    {
        return $this->col;
    }
}
