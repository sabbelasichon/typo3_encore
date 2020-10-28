<?php

namespace Ssch\Typo3Encore\Tests\Unit\Integration;

TYPO3\TestingFramework\Fluid\Unit\ViewHelpers\ViewHelperBaseTestcase

use Ssch\Typo3Encore\Integration\JsonDecodeException;
use Ssch\Typo3Encore\Integration\JsonDecoder;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \Ssch\Typo3Encore\Integration\JsonDecoder
 */
final class JsonDecoderTest extends UnitTestCase
{
    /**
     * @var JsonDecoder
     */
    protected $subject;

    protected function setUp(): void
    {
        $this->subject = new JsonDecoder();
    }

    /**
     * @test
     */
    public function decodingThrowsException(): void
    {
        $this->expectException(JsonDecodeException::class);
        $this->subject->decode('can');
    }

    /**
     * @test
     */
    public function decodeSuccessfully(): void
    {
        $this->assertEquals(['homepage' => ['js' => ['file.js'], 'css' => ['file.css']]], $this->subject->decode(file_get_contents(__DIR__ . '/../Fixtures/entrypoints.json')));
    }
}
