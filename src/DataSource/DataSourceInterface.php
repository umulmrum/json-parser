<?php

namespace Umulmrum\JsonParser\DataSource;

interface DataSourceInterface
{
    /**
     * Reads the next character, or null if no more characters are available.
     * If reading from this data source after finish() was called, the
     * behavior is undefined.
     *
     * @throws DataSourceException
     */
    public function read(): ?string;

    /**
     * Returns the line the data source operates on (1-based). New lines are recognized
     * after each "\n" character.
     */
    public function getCurrentLine(): int;

    /**
     * Returns the column the data source operates on (1-based).
     */
    public function getCurrentCol(): int;

    /**
     * Rewinds the data source by a single character.
     * If rewinding more than once before reading the next character, behavior is undefined.
     */
    public function rewind(): void;

    /**
     * Performs cleanup tasks such as closing file handles.
     */
    public function finish(): void;
}
