<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Tests\Unit\Asset;

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use Ssch\Typo3Encore\Asset\EntrypointLookup;
use Ssch\Typo3Encore\Asset\EntrypointNotFoundException;
use Ssch\Typo3Encore\Integration\FilesystemInterface;
use Ssch\Typo3Encore\Integration\JsonDecodeException;
use Ssch\Typo3Encore\Integration\JsonDecoderInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class EntrypointLookupTest extends UnitTestCase
{
    protected EntrypointLookup $subject;

    /**
     * @var JsonDecoderInterface|MockObject
     */
    protected $jsonDecoder;

    /**
     * @var FilesystemInterface|MockObject
     */
    protected $filesystem;

    protected function setUp(): void
    {
        parent::setUp();
        $this->jsonDecoder = $this->createMock(JsonDecoderInterface::class);
        $this->filesystem = $this->createMock(FilesystemInterface::class);
        $this->filesystem->method('createHash')
            ->willReturn('foobarbaz');
        $this->subject = new EntrypointLookup(
            __DIR__ . '/../Fixtures/entrypoints.json',
            true,
            $this->jsonDecoder,
            $this->filesystem,
        );
    }

    public function testIntegrityDataReturnsEmptyArray(): void
    {
        $this->filesystem->method('exists')
            ->willReturn(true);
        $this->jsonDecoder->method('decode')
            ->willReturn([
                'entrypoints' => [
                    'app' => [],
                ],
            ]);
        self::assertEmpty($this->subject->getIntegrityData());
    }

    public function testIntegrityDataReturnsCorrectValues(): void
    {
        $integrity = [
            '/typo3conf/ext/typo3_encore/Resources/Public/runtime.js' => 'sha384-GRXz+AZB+AWfcuTJbK9EZ+Na2Qa53hmwUKqRNr19Sma1DV1sYa0W7k44N7Y11Whg',
        ];

        $this->filesystem->method('exists')
            ->willReturn(true);
        $this->jsonDecoder->method('decode')
            ->willReturn([
                'entrypoints' => [
                    'app' => [],
                ],
                'integrity' =>
                 $integrity,
            ]);
        self::assertEquals($integrity, $this->subject->getIntegrityData());
    }

    public function testGetCssFiles(): void
    {
        $this->filesystem->method('exists')
            ->willReturn(true);
        $entrypoints = [
            'app' => [
                'css' => ['file.css'],
            ],
        ];
        $this->jsonDecoder->method('decode')
            ->willReturn([
                'entrypoints' => $entrypoints,
            ]);
        self::assertContains('file.css', $this->subject->getCssFiles('app'));
    }

    public function testGetFilesWithNonExistingType(): void
    {
        $this->filesystem->method('exists')
            ->willReturn(true);
        $entrypoints = [
            'app' => [
                'foo' => ['file.css'],
            ],
        ];
        $this->jsonDecoder->method('decode')
            ->willReturn([
                'entrypoints' => $entrypoints,
            ]);
        self::assertEmpty($this->subject->getCssFiles('app'));
    }

    public function testThrowsExceptionIfJsonCannotBeParsed(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->filesystem->method('exists')
            ->willReturn(true);
        $this->jsonDecoder->method('decode')
            ->willThrowException(new JsonDecodeException());
        $this->subject->getJavaScriptFiles('foo');
    }

    public function testThrowsExceptionOnEntryWithExtension(): void
    {
        $this->expectException(EntrypointNotFoundException::class);
        $this->filesystem->method('exists')
            ->willReturn(true);
        $entrypoints = [
            'app' => [
                'js' => ['file.js'],
            ],
        ];
        $this->jsonDecoder->method('decode')
            ->willReturn([
                'entrypoints' => $entrypoints,
            ]);
        self::assertEmpty($this->subject->getJavaScriptFiles('app.js'));
    }

    public function testThrowsExceptionIfEntrypointsFileDoesNotExist(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->filesystem->method('exists')
            ->willReturn(false);
        self::assertEmpty($this->subject->getJavaScriptFiles('foo'));
    }

    public function testThrowsExceptionIfJsonCanNotBeRetrieved(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->filesystem->method('exists')
            ->willReturn(true);
        self::assertEmpty($this->subject->getJavaScriptFiles('foo'));
    }

    public function testThrowsExceptionOnMissingEntrypoint(): void
    {
        $this->expectException(EntrypointNotFoundException::class);
        $this->filesystem->method('exists')
            ->willReturn(true);
        $entrypoints = [
            'app' => [
                'js' => ['file.js'],
            ],
        ];
        $this->jsonDecoder->method('decode')
            ->willReturn([
                'entrypoints' => $entrypoints,
            ]);
        self::assertEmpty($this->subject->getJavaScriptFiles('doesnotexist'));
    }

    public function testGetJsFiles(): void
    {
        $this->filesystem->method('exists')
            ->willReturn(true);
        $entrypoints = [
            'app' => [
                'js' => ['file.js'],
            ],
        ];
        $this->jsonDecoder->method('decode')
            ->willReturn([
                'entrypoints' => $entrypoints,
            ]);
        self::assertContains('file.js', $this->subject->getJavaScriptFiles('app'));
    }
}
