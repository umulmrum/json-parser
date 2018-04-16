<?php

namespace umulmrum\JsonParser\Test;

use PHPUnit\Framework\TestCase;
use umulmrum\JsonParser\DataSource\DataSourceInterface;
use umulmrum\JsonParser\DataSource\StringDataSource;
use umulmrum\JsonParser\InvalidJsonException;
use umulmrum\JsonParser\JsonParser;
use umulmrum\JsonParser\Value\ObjectValueList;

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

    /**
     * {@inheritdoc}
     */
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
            ['empty'],
            ['whitespace'],
            ['arrayEmpty'],
            ['arrayNested'],
            ['arraySingleElement'],
            ['arrayMultipleSimpleElements'],
            ['arrayMultipleArrayElements'],
            ['arrayMultipleArrayElementsWithLessWhitespace'],
            ['arrayMultipleObjectElements'],
            ['objectEmpty'],
            ['objectSingleElement'],
            ['objectMultipleSimpleElements'],
            ['objectNested'],
            ['composer'],
            ['products'], // Taken from https://www.sitepoint.com/database-json-file/
            ['tweet'], // Taken from https://www.sitepoint.com/twitter-json-example/ (and fixed)
            ['webapp'], // Taken from https://www.json.org/example.html
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
        $this->assertEquals(\json_decode($this->stringToDecode, true, 512, JSON_UNESCAPED_SLASHES), $this->actualResult);
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
            ['arrayStartOnly'],
            ['commaOnly'],
            ['objectStartOnly'],
            ['objectTrailingComma'],
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

    public function testGenerateArrayContainingArrays(): void
    {
        $this->givenADataSourceForValidFiles('arrayMultipleArrayElements');
        $this->givenAJsonParser();

        $this->whenGenerateIsCalled();
        $this->thenTheFirstArrayElementShouldBeReturned();

        $this->whenNextIsCalled();
        $this->thenTheSecondArrayElementShouldBeReturned();

        $this->whenNextIsCalled();
        $this->thenTheThirdArrayElementShouldBeReturned();
    }

    private function whenGenerateIsCalled(): void
    {
        $this->actualResult = $this->jsonParser->generate();
    }

    private function whenNextIsCalled(): void
    {
        $this->actualResult->next();
    }

    private function thenTheFirstArrayElementShouldBeReturned(): void
    {
        $this->assertEquals([
            'foo',
            'bar',
        ], $this->actualResult->current()->getValue());
    }

    private function thenTheSecondArrayElementShouldBeReturned()
    {
        $this->assertEquals([
            'bar',
            'baz',
        ], $this->actualResult->current()->getValue());
    }

    private function thenTheThirdArrayElementShouldBeReturned()
    {
        $this->assertEquals([
            'such',
            'value',
        ], $this->actualResult->current()->getValue());
    }

    public function testGenerateArrayContainingObjects(): void
    {
        $this->givenADataSourceForValidFiles('arrayMultipleObjectElements');
        $this->givenAJsonParser();

        $this->whenGenerateIsCalled();
        $this->thenTheFirstObjectElementShouldBeReturned();

        $this->whenNextIsCalled();
        $this->thenTheSecondObjectElementShouldBeReturned();

        $this->whenNextIsCalled();
        $this->thenTheThirdObjectElementShouldBeReturned();
    }

    private function thenTheFirstObjectElementShouldBeReturned()
    {
        /**
         * @var ObjectValueList $element
         */
        $element = $this->actualResult->current();
        $firstValue = $element->getFirstValue();
        $this->assertEquals(0, $element->getFirstKey());
        $this->assertEquals([
            'foo' => 'bar',
        ], [
            $firstValue->getKey() => $firstValue->getValue(),
        ]);
    }

    private function thenTheSecondObjectElementShouldBeReturned()
    {
        /**
         * @var ObjectValueList $element
         */
        $element = $this->actualResult->current();
        $firstValue = $element->getFirstValue();
        $this->assertEquals(0, $element->getFirstKey());
        $this->assertEquals([
            'bar' => 'baz',
        ], [
            $firstValue->getKey() => $firstValue->getValue(),
        ]);
    }

    private function thenTheThirdObjectElementShouldBeReturned()
    {
        /**
         * @var ObjectValueList $element
         */
        $element = $this->actualResult->current();
        $firstValue = $element->getFirstValue();
        $this->assertEquals(0, $element->getFirstKey());
        $this->assertEquals([
            'such' => 'value',
        ], [
            $firstValue->getKey() => $firstValue->getValue(),
        ]);
    }
}
