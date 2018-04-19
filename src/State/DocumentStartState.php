<?php

namespace umulmrum\JsonParser\State;

use umulmrum\JsonParser\DataSource\DataSourceInterface;

class DocumentStartState implements StateInterface
{
    use WhitespaceTrait;

    /**
     * {@inheritdoc}
     */
    public function run(DataSourceInterface $dataSource)
    {
        return null;
    }
}
