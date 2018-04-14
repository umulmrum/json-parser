<?php


namespace umulmrum\JsonParser\State;


use umulmrum\JsonParser\DataSource\DataSourceInterface;
use umulmrum\JsonParser\InvalidJsonException;
use umulmrum\JsonParser\Value\ValueInterface;

interface StateInterface
{
    public const STATE_DOCUMENT_START = 0;
    public const STATE_DOCUMENT_END = 1;
    public const STATE_OBJECT = 2;
    public const STATE_ARRAY = 3;

    /**
     * @param DataSourceInterface $dataSource
     * @return ValueInterface
     * @throws InvalidJsonException
     */
    public function run(DataSourceInterface $dataSource): ?ValueInterface;
}