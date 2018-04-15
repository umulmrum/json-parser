<?php


namespace umulmrum\JsonParser\Test\DataSource;


use PHPUnit\Framework\TestCase;
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

    protected function tearDown()
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
            [ 'empty.txt' ],
            [ 'singleChar.txt' ],
            [ 'singleUmlaut.txt', ],
            [ 'exceedBufferSize.txt', ],
            [ 'umlautsOnBufferEdges.txt', ],
        ];
    }

    private function givenAFileDataSource(string $fileName): void
    {
        $this->dataSource = new FileDataSource($this->getFilePath($fileName), 10);
    }

    private function getFilePath(string $fileName): string
    {
        return __DIR__ . '/fixtures/' . $fileName;
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
        $this->assertEquals(\file_get_contents($this->getFilePath($fileName)), $this->actualResult);
    }
}