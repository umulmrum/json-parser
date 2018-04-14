<?php


namespace umulmrum\JsonParser\Test\DataSource;


use PHPUnit\Framework\TestCase;
use umulmrum\JsonParser\DataSource\StringDataSource;

class StringDataSourceTest extends TestCase
{
    /**
     * @var StringDataSource
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
     * @param string $data
     */
    public function testReadData(string $data): void
    {
        $this->givenAStringDataSource($data);
        $this->whenAllDataIsReadFromTheDataSource();
        $this->thenTheResultingStringShouldEqual($data);
    }

    public function provideDataForTestReadData(): array
    {
        return [
            [
                '',
            ],
            [
                'test',
            ],
            [
                "test\nfoobar\nsuchstring",
            ],
            [
                '{ "" : ""}',
            ],
            [
                '{ "ä" : "ö"}',
            ],
        ];
    }

    private function givenAStringDataSource(string $data): void
    {
        $this->dataSource = new StringDataSource($data);
    }

    private function whenAllDataIsReadFromTheDataSource(): void
    {
        $this->actualResult = '';
        while (null !== $char = $this->dataSource->read()) {
            $this->actualResult .= $char;
        }
    }

    private function thenTheResultingStringShouldEqual(string $data): void
    {
        $this->assertEquals($data, $this->actualResult);
    }
}