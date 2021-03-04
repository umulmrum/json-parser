<?php

namespace Umulmrum\JsonParser\Test\DataSource;

use PHPUnit\Framework\TestCase;
use Umulmrum\JsonParser\DataSource\StringDataSource;

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
        $this->dataSource->finish();
    }

    private function thenTheResultingStringShouldEqual(string $data): void
    {
        self::assertEquals($data, $this->actualResult);
    }
}
