<?php

namespace Umulmrum\JsonParser\State;

use Umulmrum\JsonParser\DataSource\DataSourceInterface;

/**
 * @internal
 */
class DocumentEndState implements StateInterface
{
    /**
     * {@inheritdoc}
     */
    public function run(DataSourceInterface $dataSource)
    {
        return null;
    }
}
