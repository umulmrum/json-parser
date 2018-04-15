<?php


namespace umulmrum\JsonParser\DataSource;


interface DataSourceInterface
{
    public function read(): ?string;

    public function getCurrentLine(): int;

    public function getCurrentCol(): int;

    public function rewind(): void;

    public function finish(): void;
}