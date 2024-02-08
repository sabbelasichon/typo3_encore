<?php

declare(strict_types=1);

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
use UnexpectedValueException;

final class JsonDecoderTest extends UnitTestCase
{
    protected JsonDecoder $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new JsonDecoder();
    }

    public function testDecodingThrowsException(): void
    {
        $this->expectException(JsonDecodeException::class);
        $this->subject->decode('can');
    }

    public function testDecodeSuccessfully(): void
    {
        $jsonFile = file_get_contents(__DIR__ . '/../Fixtures/entrypoints.json');

        if ($jsonFile === false) {
            throw new UnexpectedValueException('Could not open json file');
        }

        self::assertEquals([
            'homepage' => [
                'js' => ['file.js'],
                'css' => ['file.css'],
            ],
        ], $this->subject->decode($jsonFile));
    }
}
