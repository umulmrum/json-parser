<?php

namespace umulmrum\JsonParser\DataSource;

class FileDataSource extends AbstractDataSource
{
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
    private $rewound;

    /**
     * @param string $filePath
     * @param int    $bufferSize
     *
     * @throws DataSourceException
     */
    public function __construct(string $filePath, int $bufferSize = 2048)
    {
        if (false === \file_exists($filePath)) {
            throw new DataSourceException('File does not exist: '.$filePath);
        }
        if (false === \is_readable($filePath)) {
            throw new DataSourceException('File is not readable: '.$filePath);
        }

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
        if ($this->rewound) {
            if ($this->lastChar === null) {
                throw new DataSourceException('Cannot rewind more than once');
            }
            $char = $this->lastChar;
            $this->lastChar = null;
            $this->rewound = false;

            return $char;
        }

        $char = $this->readFromStream();
        if ($char === '') {
            return null;
        }
        /*
         * Handle UTF-8 multibyte characters (https://stackoverflow.com/questions/3666306/how-to-iterate-utf-8-string-in-php)
         */
        $ord = \ord($char);
        if ($ord > 127) {
            $char .= $this->readFromStream();
            if ($ord > 223) {
                $char .= $this->readFromStream();
                if ($ord > 239) {
                    $char .= $this->readFromStream();
                }
            }
        }

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
     * @throws DataSourceException
     */
    private function readFromStream(): string
    {
        $char = \stream_get_contents($this->fileHandle, 1);
        if ($char === false) {
            throw new DataSourceException('Error while reading from stream');
        }

        return $char;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind(): void
    {
        $this->rewound = true;
        // TODO line and col do not match after rewind.
    }

    /**
     * {@inheritdoc}
     */
    public function finish(): void
    {
        \fclose($this->fileHandle);
    }
}
