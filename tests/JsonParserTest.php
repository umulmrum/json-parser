<?php


namespace umulmrum\JsonParser\Test;


use PHPUnit\Framework\TestCase;
use umulmrum\JsonParser\DataSource\DataSourceInterface;
use umulmrum\JsonParser\DataSource\StringDataSource;
use umulmrum\JsonParser\InvalidJsonException;
use umulmrum\JsonParser\JsonParser;
use umulmrum\JsonParser\Value\ObjectValue;

class JsonParserTest extends TestCase
{
    /**
     * @var DataSourceInterface
     */
    private $dataSource;
    /**
     * @var string
     */
    private $stringToDecode;
    /**
     * @var JsonParser
     */
    private $jsonParser;
    /**
     * @var array|\Generator
     */
    private $actualResult;

    protected function tearDown()
    {
        parent::tearDown();
        $this->dataSource = null;
        $this->stringToDecode = null;
        $this->jsonParser = null;
        $this->actualResult = null;
    }

    /**
     * @dataProvider provideDataForTestAllValid
     *
     * @param string $fileToCheck
     */
    public function testAllValid(string $fileToCheck): void
    {
        $this->givenADataSourceForValidFiles($fileToCheck);
        $this->givenAJsonParser();

        $this->whenAllIsCalled();

        $this->thenTheResultShouldBeEqualToJsonDecode();
    }

    public function provideDataForTestAllValid(): array
    {
        return [
            [ 'empty' ],
            [ 'whitespace' ],
            [ 'arrayEmpty' ],
            [ 'arrayNested' ],
            [ 'arraySingleElement' ],
            [ 'arrayMultipleSimpleElements' ],
            [ 'arrayMultipleArrayElements' ],
            [ 'objectEmpty' ],
            [ 'objectSingleElement' ],
            [ 'objectMultipleSimpleElements' ],
            [ 'objectNested' ],
            [ 'composer' ],
            [ 'products' ], // Taken from https://www.sitepoint.com/database-json-file/
            [ 'tweet' ], // Taken from https://www.sitepoint.com/twitter-json-example/ (and fixed)
            [ 'webapp' ], // Taken from https://www.json.org/example.html
        ];
    }

    private function givenADataSourceForValidFiles(string $fileToCheck): void
    {
        $filePath = sprintf('%s/fixtures/valid/%s.json', __DIR__, $fileToCheck);
        $this->stringToDecode = \file_get_contents($filePath);
        $this->dataSource = new StringDataSource($this->stringToDecode);
    }

    private function givenAJsonParser(): void
    {
        $this->jsonParser = new JsonParser($this->dataSource);
    }

    private function whenAllIsCalled(): void
    {
        $this->actualResult = $this->jsonParser->all();
    }

    private function thenTheResultShouldBeEqualToJsonDecode(): void
    {
        $this->assertEquals(\json_decode($this->stringToDecode, true), $this->actualResult);
    }

    /**
     * @dataProvider provideDataForTestAllInvalid
     *
     * @param string $fileToCheck
     */
    public function testAllInvalid(string $fileToCheck): void
    {
        $this->givenADataSourceForInvalidFiles($fileToCheck);
        $this->givenAJsonParser();

        $this->thenAnInvalidJsonExceptionShouldBeThrown();

        $this->whenAllIsCalled();
    }

    public function provideDataForTestAllInvalid(): array
    {
        return [
            [ 'arrayStartOnly' ],
            [ 'commaOnly' ],
            [ 'objectStartOnly' ],
            [ 'objectTrailingComma' ],
        ];
    }

    private function givenADataSourceForInvalidFiles(string $fileToCheck): void
    {
        $filePath = sprintf('%s/fixtures/invalid/%s.json', __DIR__, $fileToCheck);
        $this->stringToDecode = \file_get_contents($filePath);
        $this->dataSource = new StringDataSource($this->stringToDecode);
    }

    private function thenAnInvalidJsonExceptionShouldBeThrown(): void
    {
        $this->expectException(InvalidJsonException::class);
    }

    public function testGenerate(): void
    {
        $this->givenADataSourceForValidFiles('arrayMultipleArrayElements');
        $this->givenAJsonParser();

        $this->whenGenerateIsCalled();
        $this->thenTheFirstElementShouldBeReturned();
    }

    private function whenGenerateIsCalled(): void
    {
        $this->actualResult = $this->jsonParser->generate();
    }

    private function thenTheFirstElementShouldBeReturned(): void
    {
        $this->assertEquals([
            'foo',
            'bar',
        ], $this->actualResult->current()->getValue());
    }
}