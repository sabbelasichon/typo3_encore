<?php

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Tests\Unit\Integration;

use Ssch\Typo3Encore\Integration\JsonDecodeException;
use Ssch\Typo3Encore\Integration\JsonDecoder;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \Ssch\Typo3Encore\Integration\JsonDecoder
 */
final class JsonDecoderTest extends UnitTestCase
{
    protected JsonDecoder $subject;

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
        self::assertEquals(['homepage' => ['js' => ['file.js'], 'css' => ['file.css']]], $this->subject->decode(file_get_contents(__DIR__ . '/../Fixtures/entrypoints.json')));
    }
}
