<?php

namespace umulmrum\JsonParser\DataSource;

class FileDataSource extends AbstractDataSource
{
    /**
     * @var int
     */
    private $bufferSize;
    /**
     * @var string
     */
    private $buffer;
    /**
     * @var resource
     */
    private $fileHandle;
    /**
     * @var int
     */
    private $position = 0;
    /**
     * @var string
     */
    private $lastChar;
    /**
     * @var bool
     */
    private $isRewound;
    /**
     * @var int
     */
    private $actualBufferSize = 0;

    /**
     * @param string $filePath
     * @param int    $bufferSize
     *
     * @throws DataSourceException
     */
    public function __construct(string $filePath, int $bufferSize = 4096)
    {
        if (false === \file_exists($filePath)) {
            throw new DataSourceException('File does not exist: '.$filePath);
        }
        if (false === \is_readable($filePath)) {
            throw new DataSourceException('File is not readable: '.$filePath);
        }

        $this->bufferSize = $bufferSize;
        $this->fileHandle = \fopen($filePath, 'rb');
        if (false === $this->fileHandle) {
            throw new DataSourceException('File could not be opened: '.$filePath);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function read(): ?string
    {
        if (true === $this->isRewound) {
            $char = $this->lastChar;
            $this->lastChar = null;
            $this->isRewound = false;
            // TODO line and col do not match after rewind.

            return $char;
        }
        if ($this->position === $this->actualBufferSize) {
            /*
             * Using preg_split instead of mb_substr as suggested in
             * https://stackoverflow.com/questions/3666306/how-to-iterate-utf-8-string-in-php
             */
            $this->buffer = \preg_split(
                '//u',
                \stream_get_contents($this->fileHandle, $this->bufferSize),
                -1,
                PREG_SPLIT_NO_EMPTY
            );
            if (false === $this->buffer) {
                throw new DataSourceException('Error while reading from file.');
            }
            $this->actualBufferSize = \count($this->buffer);
            $this->position = 0;
            if (0 === $this->actualBufferSize) {
                return null;
            }
        }
        $char = $this->buffer[$this->position];
        ++$this->position;
        if ("\n" === $this->lastChar) {
            ++$this->line;
            $this->col = 1;
        } else {
            ++$this->col;
        }
        $this->lastChar = $char;

        return $char;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind(): void
    {
        $this->isRewound = true;
    }

    /**
     * {@inheritdoc}
     */
    public function finish(): void
    {
        \fclose($this->fileHandle);
    }
}
