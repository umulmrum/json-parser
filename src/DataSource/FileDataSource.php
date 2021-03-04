<?php

namespace Umulmrum\JsonParser\DataSource;

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
     * @var string|null
     */
    private $lastChar;
    /**
     * @var bool
     */
    private $rewound = false;

    /**
     * @throws DataSourceException
     */
    public function __construct(string $filePath)
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
        if ('stream' !== \get_resource_type($this->fileHandle)) {
            throw new DataSourceException('Data source is already finished, cannot read.');
        }
        if ($this->rewound) {
            if (null === $this->lastChar) {
                throw new DataSourceException('Cannot rewind more than once');
            }
            $char = $this->lastChar;
            $this->lastChar = null;
            $this->rewound = false;

            return $char;
        }

        $char = $this->readCharacterFromStream();
        if ('' === $char) {
            return null;
        }

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
    private function readCharacterFromStream(): ?string
    {
        $char = $this->readFromStream();
        if ('' === $char) {
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

        return $char;
    }

    /**
     * @throws DataSourceException
     */
    private function readFromStream(): string
    {
        $char = \stream_get_contents($this->fileHandle, 1);
        if (false === $char) {
            throw new DataSourceException('Error while reading from stream');
        }
        ++$this->position;

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
        /**
         * @psalm-suppress InvalidPropertyAssignmentValue
         */
        \fclose($this->fileHandle);
    }
}
