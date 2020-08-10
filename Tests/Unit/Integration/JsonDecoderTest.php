<?php

namespace Ssch\Typo3Encore\Tests\Unit\Integration;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

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
