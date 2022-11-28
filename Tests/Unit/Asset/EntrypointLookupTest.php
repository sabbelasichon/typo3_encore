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
use Ssch\Typo3Encore\Integration\CacheFactory;
use Ssch\Typo3Encore\Integration\FilesystemInterface;
use Ssch\Typo3Encore\Integration\JsonDecodeException;
use Ssch\Typo3Encore\Integration\JsonDecoderInterface;
use TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class EntrypointLookupTest extends UnitTestCase
{
    /**
     * @var string
     */
    private const CACHE_KEY_PREFIX = 'cacheKeyPrefix';

    protected EntrypointLookup $subject;

    /**
     * @var JsonDecoderInterface|MockObject
     */
    protected $jsonDecoder;

    /**
     * @var FilesystemInterface|MockObject
     */
    protected $filesystem;

    /**
     * @var CacheFactory|MockObject
     */
    protected $cacheFactory;

    protected string $cacheKey;

    /**
     * @var FrontendInterface|MockObject
     */
    protected $cache;

    protected function setUp(): void
    {
        parent::setUp();
        $this->jsonDecoder = $this->getMockBuilder(JsonDecoderInterface::class)->getMock();
        $this->filesystem = $this->getMockBuilder(FilesystemInterface::class)->getMock();
        $this->filesystem->method('createHash')
            ->willReturn('foobarbaz');
        $this->cacheFactory = $this->getMockBuilder(CacheFactory::class)->disableOriginalConstructor()->getMock();
        $this->cache = $this->getMockBuilder(FrontendInterface::class)->getMock();
        $this->cacheFactory->method('createInstance')
            ->willReturn($this->cache);
        $this->subject = new EntrypointLookup(
            __DIR__ . '/../Fixtures/entrypoints.json',
            self::CACHE_KEY_PREFIX,
            true,
            $this->jsonDecoder,
            $this->filesystem,
            $this->cacheFactory
        );
        $this->cacheKey = sprintf('%s-%s-%s', self::CACHE_KEY_PREFIX, CacheFactory::CACHE_KEY, 'foobarbaz');
    }

    public function testNoSuchCacheExceptionIsThrown(): void
    {
        $cacheFactory = $this->getMockBuilder(CacheFactory::class)->disableOriginalConstructor()->getMock();
        $cacheFactory->method('createInstance')
            ->willThrowException(new NoSuchCacheException());
        $this->expectException(NoSuchCacheException::class);
        $subject = new EntrypointLookup('foo', 'bar', true, $this->jsonDecoder, $this->filesystem, $cacheFactory);
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

    public function testGetFromCache(): void
    {
        $this->filesystem->expects(self::never())->method('exists');

        $entrypoints = [
            'app' => [
                'css' => ['file.css'],
            ],
        ];

        $this->cache->method('has')
            ->with($this->cacheKey)
            ->willReturn(true);
        $this->cache->method('get')
            ->with($this->cacheKey)
            ->willReturn([
                'entrypoints' => $entrypoints,
            ]);

        self::assertContains('file.css', $this->subject->getCssFiles('app'));
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
