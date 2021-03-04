<?php

namespace Umulmrum\JsonParser\State;

use Umulmrum\JsonParser\DataSource\DataSourceException;
use Umulmrum\JsonParser\DataSource\DataSourceInterface;
use Umulmrum\JsonParser\InvalidJsonException;

/**
 * @internal
 */
interface StateInterface
{
    /**
     * Returns the value that is the result of handling the state.
     *
     * @return mixed
     *
     * @throws DataSourceException
     * @throws InvalidJsonException
     */
    public function run(DataSourceInterface $dataSource);
}
