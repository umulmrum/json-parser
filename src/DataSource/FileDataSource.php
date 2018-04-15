<?php


namespace umulmrum\JsonParser\DataSource;


class FileDataSource extends AbstractDataSource
{
    /**
     * @var string
     */
    private $filePath;
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
     *
     * @throws DataSourceException
     */
    public function __construct(string $filePath, int $bufferSize = 1000)
    {
        if (false === \file_exists($filePath)) {
            throw new DataSourceException('File does not exist: ' . $filePath);
        }
        if (false === \is_readable($filePath)) {
            throw new DataSourceException('File is not readable' . $filePath);
        }

        $this->filePath = $filePath;
        $this->bufferSize = $bufferSize;
        $this->fileHandle = \fopen($filePath, 'rb');
    }

    public function read(): ?string
    {
        if (true === $this->isRewound) {
            $char = $this->lastChar;
            $this->isRewound = false;
            // TODO line and col do not match after rewind.

            return $char;
        }
        if ($this->position === $this->actualBufferSize
            || null === $this->buffer) {
            if (true === feof($this->fileHandle)) {
                return null;
            }
            $this->buffer = \fread($this->fileHandle, $this->bufferSize);
            $this->actualBufferSize = mb_strlen($this->buffer);
            $this->position = 0;
            if ('' === $this->buffer && true === feof($this->fileHandle)) {
                return null;
            }
        }
        $char = mb_substr($this->buffer, $this->position, 1);
        $this->position++;
        if (true === $this->lastChar) {
            $this->line++;
            $this->col = 1;
            $this->lastChar = false;
        } else {
            $this->col++;
        }

        if ("\n" === $char) {
            $this->lastChar = true;
        }

        return $char;
    }

    public function rewind(): void
    {
        $this->isRewound = true;
    }

    public function finish(): void
    {
        \fclose($this->fileHandle);
    }
}