<?php

namespace umulmrum\JsonParser\DataSource;

interface DataSourceInterface
{
    /**
     * Reads the next character, or null if no more characters are available.
     * If reading from this data source after finish() was called, the
     * behavior is undefined.
     *
     * @return null|string
     *
     * @throws DataSourceException
     */
    public function read(): ?string;

    /**
     * Returns the line the data source operates on (1-based). New lines are recognized
     * after each "\n" character.
     *
     * @return int
     */
    public function getCurrentLine(): int;

    /**
     * Returns the column the data source operates on (1-based).
     *
     * @return int
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
