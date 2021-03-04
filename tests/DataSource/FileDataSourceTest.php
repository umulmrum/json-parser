<?php

namespace umulmrum\JsonParser\Test\DataSource;

use PHPUnit\Framework\TestCase;
use umulmrum\JsonParser\DataSource\DataSourceException;
use umulmrum\JsonParser\DataSource\FileDataSource;

class FileDataSourceTest extends TestCase
{
    /**
     * @var FileDataSource
     */
    private $dataSource;
    /**
     * @var string
     */
    private $actualResult;

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->dataSource = null;
        $this->actualResult = null;
    }

    /**
     * @dataProvider provideDataForTestReadData
     *
     * @param string $fileName
     */
    public function testReadData(string $fileName): void
    {
        $this->givenAFileDataSource($fileName);

        $this->whenAllDataIsReadFromTheDataSource();

        $this->thenTheResultingStringShouldEqualContentsFrom($fileName);
    }

    public function provideDataForTestReadData(): array
    {
        return [
            ['empty.txt'],
            ['singleChar.txt'],
            ['singleUmlaut.txt'],
            ['multipleChars.txt'],
            ['multipleUmlauts.txt'],
        ];
    }

    private function givenAFileDataSource(string $fileName): void
    {
        $this->dataSource = new FileDataSource($this->getFilePath($fileName));
    }

    private function getFilePath(string $fileName): string
    {
        return __DIR__.'/fixtures/'.$fileName;
    }

    private function whenAllDataIsReadFromTheDataSource(): void
    {
        $this->actualResult = '';
        while (null !== $char = $this->dataSource->read()) {
            $this->actualResult .= $char;
        }
        $this->dataSource->finish();
    }

    private function thenTheResultingStringShouldEqualContentsFrom(string $fileName): void
    {
        self::assertStringEqualsFile($this->getFilePath($fileName), $this->actualResult);
    }

    public function testRewind(): void
    {
        $this->givenAFileDataSource('multipleChars.txt');

        $this->whenReadIsCalled();
        $this->thenTheDataSourceShouldReturn('a');

        $this->whenReadIsCalled();
        $this->thenTheDataSourceShouldReturn('b');

        $this->whenRewindIsCalled();
        $this->whenReadIsCalled();
        $this->thenTheDataSourceShouldReturn('b');
    }

    private function whenReadIsCalled(): void
    {
        $this->actualResult = $this->dataSource->read();
    }

    private function thenTheDataSourceShouldReturn(string $char): void
    {
        self::assertEquals($char, $this->actualResult);
    }

    private function whenRewindIsCalled(): void
    {
        $this->dataSource->rewind();
    }

    public function testInvalidFile(): void
    {
        $this->thenDataSourceExceptionIsExpected();
        $this->givenAFileDataSource('foo.txt');
    }

    private function thenDataSourceExceptionIsExpected(): void
    {
        $this->expectException(DataSourceException::class);
    }
}
