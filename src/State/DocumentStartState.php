<?php


namespace umulmrum\JsonParser\State;


use umulmrum\JsonParser\DataSource\DataSourceInterface;
use umulmrum\JsonParser\Value\ValueInterface;

class DocumentStartState implements StateInterface
{
    use WhitespaceTrait;

    /**
     * {@inheritdoc}
     */
    public function run(DataSourceInterface $dataSource): ?ValueInterface
    {
        return null;
    }
}