<?php

namespace umulmrum\JsonParser\Test;

use PHPUnit\Framework\TestCase;
use umulmrum\JsonParser\DataSource\DataSourceInterface;
use umulmrum\JsonParser\DataSource\StringDataSource;
use umulmrum\JsonParser\InvalidJsonException;
use umulmrum\JsonParser\JsonParser;

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
     * @var InvalidJsonException
     */
    private $thrownException;

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->dataSource = null;
        $this->stringToDecode = null;
        $this->jsonParser = null;
        $this->actualResult = null;
        $this->thrownException = null;
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
        return $this->getTestCasesFromPath(__DIR__.'/fixtures/valid');
    }

    private function getTestCasesFromPath(string $path): array
    {
        $dir = \opendir($path);
        $files = [];

        while (false !== ($entry = \readdir($dir))) {
            if ('.' === $entry || '..' === $entry) {
                continue;
            }

            $files[] = [$entry];
        }

        \closedir($dir);

        return $files;
    }

    private function givenADataSourceForValidFiles(string $fileToCheck): void
    {
        $filePath = sprintf('%s/fixtures/valid/%s', __DIR__, $fileToCheck);
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
        self::assertEquals(\json_decode($this->stringToDecode, true, 512, JSON_UNESCAPED_SLASHES), $this->actualResult);
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
        return $this->getTestCasesFromPath(__DIR__.'/fixtures/invalid');
    }

    private function givenADataSourceForInvalidFiles(string $fileToCheck): void
    {
        $filePath = sprintf('%s/fixtures/invalid/%s', __DIR__, $fileToCheck);
        $this->stringToDecode = \file_get_contents($filePath);
        $this->dataSource = new StringDataSource($this->stringToDecode);
    }

    private function thenAnInvalidJsonExceptionShouldBeThrown(): void
    {
        $this->expectException(InvalidJsonException::class);
    }

    public function testGenerateArrayContainingArrays(): void
    {
        $this->givenADataSourceForValidFiles('arrayMultipleArrayElements.json');
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
        $element = $this->actualResult->current();
        self::assertEquals(0, \key($element));
        self::assertEquals([
            'foo',
            'bar',
        ], \current($element));
    }

    private function thenTheSecondArrayElementShouldBeReturned()
    {
        $element = $this->actualResult->current();
        self::assertEquals(1, \key($element));
        self::assertEquals([
            'bar',
            'baz',
        ], \current($element));
    }

    private function thenTheThirdArrayElementShouldBeReturned()
    {
        $element = $this->actualResult->current();
        self::assertEquals(2, \key($element));
        self::assertEquals([
            'such',
            'value',
        ], \current($element));
    }

    public function testGenerateArrayContainingObjects(): void
    {
        $this->givenADataSourceForValidFiles('arrayMultipleObjectElements.json');
        $this->givenAJsonParser();

        $this->whenGenerateIsCalled();
        $this->thenTheFirstObjectElementShouldBeReturned(0);

        $this->whenNextIsCalled();
        $this->thenTheSecondObjectElementShouldBeReturned(1);

        $this->whenNextIsCalled();
        $this->thenTheThirdObjectElementShouldBeReturned(2);
    }

    private function thenTheFirstObjectElementShouldBeReturned($expectedKey)
    {
        $element = $this->actualResult->current();
        self::assertEquals($expectedKey, \key($element));
        self::assertEquals([
            'foo' => 'bar',
        ], \current($element));
    }

    private function thenTheSecondObjectElementShouldBeReturned($expectedKey)
    {
        $element = $this->actualResult->current();
        self::assertEquals($expectedKey, \key($element));
        self::assertEquals([
            'bar' => 'baz',
            ], \current($element));
    }

    private function thenTheThirdObjectElementShouldBeReturned($expectedKey)
    {
        $element = $this->actualResult->current();
        self::assertEquals($expectedKey, \key($element));
        self::assertEquals([
            'such' => 'value',
        ], \current($element));
    }

    public function testGenerateObjectContainingObjects(): void
    {
        $this->givenADataSourceForValidFiles('objectMultipleObjectElements.json');
        $this->givenAJsonParser();

        $this->whenGenerateIsCalled();
        $this->thenTheFirstObjectElementShouldBeReturned('key1');

        $this->whenNextIsCalled();
        $this->thenTheSecondObjectElementShouldBeReturned('key2');

        $this->whenNextIsCalled();
        $this->thenTheThirdObjectElementShouldBeReturned('key3');
    }

    /**
     * @dataProvider provideDataForTestAllValid
     *
     * @param string $fileToCheck
     */
    public function testGenerateValid(string $fileToCheck): void
    {
        $this->givenADataSourceForValidFiles($fileToCheck);
        $this->givenAJsonParser();
        $this->whenGenerateIsCalledUntilTheDataSourceIsDepleted();
        $this->thenTheResultShouldBeEqualToJsonDecode();
    }

    private function whenGenerateIsCalledUntilTheDataSourceIsDepleted(): void
    {
        $this->actualResult = [];
        $hasResult = false;
        foreach ($this->jsonParser->generate() as $value) {
            if (null !== $value) {
                $key = \key($value);
                if (null === $key) {
                    $this->actualResult = [];
                    $hasResult = true;

                    break;
                }
                $hasResult = true;
                $this->actualResult[$key] = \current($value);
            }
        }
        if (false === $hasResult) {
            $this->actualResult = null;
        }
    }

    /**
     * @dataProvider provideDataForTestErrorInfo
     *
     * @param string $fileToCheck
     * @param int    $expectedErrorLine
     * @param int    $expectedErrorColumn
     */
    public function testErrorInfo(string $fileToCheck, int $expectedErrorLine, int $expectedErrorColumn): void
    {
        self::markTestSkipped('Line and column info do not work correctly yet after rewinding.');
        $this->givenADataSourceForInvalidFiles($fileToCheck);
        $this->givenAJsonParser();

        $this->whenAllIsCalledForInvalidString();

        $this->thenTheExceptionShouldReportCorrectLineAndColumn($expectedErrorLine, $expectedErrorColumn);
    }

    public function provideDataForTestErrorInfo(): array
    {
        return [
            [
                'commaOnly.json',
                1,
                2,
            ],
            [
                'objectValueDoubleKey.json',
                4,
                36,
            ],
        ];
    }

    private function whenAllIsCalledForInvalidString(): void
    {
        try {
            $this->jsonParser->all();
            self::fail('Expected InvalidJsonException.');
        } catch (InvalidJsonException $e) {
            $this->thrownException = $e;
        }
    }

    private function thenTheExceptionShouldReportCorrectLineAndColumn($expectedErrorLine, $expectedErrorColumn): void
    {
        self::assertEquals($expectedErrorLine, $this->thrownException->getJsonLine());
        self::assertEquals($expectedErrorColumn, $this->thrownException->getJsonCol());
    }
}
