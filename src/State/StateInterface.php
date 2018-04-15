<?php


namespace umulmrum\JsonParser\State;


use umulmrum\JsonParser\DataSource\DataSourceException;
use umulmrum\JsonParser\DataSource\DataSourceInterface;
use umulmrum\JsonParser\InvalidJsonException;
use umulmrum\JsonParser\Value\ValueInterface;

interface StateInterface
{
    /**
     * Returns the value that is the result of handling the state.
     *
     * @param DataSourceInterface $dataSource
     *
     * @return ValueInterface
     *
     * @throws DataSourceException
     * @throws InvalidJsonException
     */
    public function run(DataSourceInterface $dataSource): ?ValueInterface;
}