<?php


namespace umulmrum\JsonParser\DataSource;


class FileDataSource extends AbstractDataSource
{
    /**
     * @var int
     */
    private $bufferSize;

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
    private $actualBufferSize;

    /**
     * @param string $filePath
     * @param int $bufferSize
     *
     * @throws DataSourceException
     */
    public function __construct(string $filePath, int $bufferSize = 100)
    {
        if (false === \file_exists($filePath)) {
            throw new DataSourceException('File does not exist: ' . $filePath);
        }
        if (false === \is_readable($filePath)) {
            throw new DataSourceException('File is not readable: ' . $filePath);
        }

        $this->bufferSize = $bufferSize;
        $this->fileHandle = \fopen($filePath, 'rb');
        if (false === $this->fileHandle) {
            throw new DataSourceException('File could not be opened: ' . $filePath);
        }
    }

    /**
     * {@inheritDoc}
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
        if ($this->position === $this->actualBufferSize
            || null === $this->buffer) {
            if (true === \feof($this->fileHandle)) {
                return null;
            }
            $this->buffer = \fread($this->fileHandle, $this->bufferSize);
            if (false === $this->buffer) {
                throw new DataSourceException('Error while reading from file.');
            }
            $this->actualBufferSize = mb_strlen($this->buffer);
            $this->position = 0;
            if ('' === $this->buffer && true === \feof($this->fileHandle)) {
                return null;
            }
        }
        $char = mb_substr($this->buffer, $this->position, 1);
        $this->position++;
        if ("\n" === $this->lastChar) {
            $this->line++;
            $this->col = 1;
        } else {
            $this->col++;
        }
        $this->lastChar = $char;

        return $char;
    }

    /**
     * {@inheritDoc}
     */
    public function rewind(): void
    {
        $this->isRewound = true;
    }

    /**
     * {@inheritDoc}
     */
    public function finish(): void
    {
        \fclose($this->fileHandle);
    }
}