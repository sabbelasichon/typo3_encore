<?php

namespace Ssch\Typo3Encore\Tests\Unit\Asset;

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

use InvalidArgumentException;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Ssch\Typo3Encore\Asset\EntrypointLookup;
use Ssch\Typo3Encore\Asset\EntrypointNotFoundException;
use Ssch\Typo3Encore\Integration\CacheFactory;
use Ssch\Typo3Encore\Integration\FilesystemInterface;
use Ssch\Typo3Encore\Integration\JsonDecodeException;
use Ssch\Typo3Encore\Integration\JsonDecoderInterface;
use TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;

/**
 * @covers \Ssch\Typo3Encore\Asset\EntrypointLookup
 */
class EntrypointLookupTest extends UnitTestCase
{
    const CACHE_KEY_PREFIX = 'cacheKeyPrefix';
    private $subject;

    /**
     * @var JsonDecoderInterface|MockObject
     */
    private $jsonDecoder;

    /**
     * @var FilesystemInterface|MockObject
     */
    private $filesystem;

    /**
     * @var CacheFactory|MockObject
     */
    private $cacheFactory;

    /**
     * @var string
     */
    private $cacheKey;

    /**
     * @var FrontendInterface|MockObject
     */
    private $cache;

    protected function setUp()
    {
        $this->jsonDecoder = $this->getMockBuilder(JsonDecoderInterface::class)->getMock();
        $this->filesystem = $this->getMockBuilder(FilesystemInterface::class)->getMock();
        $this->cacheFactory = $this->getMockBuilder(CacheFactory::class)->disableOriginalConstructor()->getMock();
        $this->cache = $this->getMockBuilder(FrontendInterface::class)->getMock();
        $this->cacheFactory->method('createInstance')->willReturn($this->cache);
        $this->subject = new EntrypointLookup(__DIR__ . '/../Fixtures/entrypoints.json', self::CACHE_KEY_PREFIX, $this->jsonDecoder, $this->filesystem, $this->cacheFactory);
        $this->cacheKey = sprintf('%s-%s', self::CACHE_KEY_PREFIX, CacheFactory::CACHE_KEY);
    }

    /**
     * @test
     */
    public function noSuchCacheExceptionIsThrown()
    {
        $cacheFactory = $this->getMockBuilder(CacheFactory::class)->disableOriginalConstructor()->getMock();
        $cacheFactory->method('createInstance')->willThrowException(new NoSuchCacheException());
        $this->expectException(NoSuchCacheException::class);
        $subject = new EntrypointLookup('foo', 'bar', $this->jsonDecoder, $this->filesystem, $cacheFactory);
    }

    /**
     * @test
     */
    public function integrityDataReturnsEmptyArray()
    {
        $this->filesystem->method('exists')->willReturn(true);
        $this->jsonDecoder->method('decode')->willReturn(['entrypoints' => ['app' => []]]);
        $this->assertEmpty($this->subject->getIntegrityData());
    }

    /**
     * @test
     */
    public function integrityDataReturnsCorrectValues()
    {
        $integrity = ['/typo3conf/ext/typo3_encore/Resources/Public/runtime.js' => 'sha384-GRXz+AZB+AWfcuTJbK9EZ+Na2Qa53hmwUKqRNr19Sma1DV1sYa0W7k44N7Y11Whg'];

        $this->filesystem->method('exists')->willReturn(true);
        $this->jsonDecoder->method('decode')->willReturn(['entrypoints' => ['app' => []], 'integrity' => $integrity]);
        $this->assertEquals($integrity, $this->subject->getIntegrityData());
    }

    /**
     * @test
     */
    public function getCssFiles()
    {
        $this->filesystem->method('exists')->willReturn(true);
        $entrypoints = [
            'app' => [
                'css' => [
                    'file.css'
                ]
            ],
        ];
        $this->jsonDecoder->method('decode')->willReturn(['entrypoints' => $entrypoints]);
        $this->assertContains('file.css', $this->subject->getCssFiles('app'));
    }

    /**
     * @test
     */
    public function getFilesWithNonExistingType()
    {
        $this->filesystem->method('exists')->willReturn(true);
        $entrypoints = [
            'app' => [
                'foo' => [
                    'file.css'
                ]
            ],
        ];
        $this->jsonDecoder->method('decode')->willReturn(['entrypoints' => $entrypoints]);
        $this->assertEmpty($this->subject->getCssFiles('app'));
    }

    /**
     * @test
     */
    public function getFromCache()
    {
        $this->filesystem->expects($this->never())->method('exists');
        $entrypoints = [
            'app' => [
                'css' => [
                    'file.css'
                ]
            ],
        ];

        $this->cache->method('has')->with($this->cacheKey)->willReturn(true);
        $this->cache->method('get')->with($this->cacheKey)->willReturn(['entrypoints' => $entrypoints]);

        $this->assertContains('file.css', $this->subject->getCssFiles('app'));
    }

    /**
     * @test
     */
    public function throwsExceptionIfJsonCannotBeParsed()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->filesystem->method('exists')->willReturn(true);
        $this->jsonDecoder->method('decode')->willThrowException(new JsonDecodeException());
        $this->subject->getJavaScriptFiles('foo');
    }

    /**
     * @test
     */
    public function throwsExceptionOnEntryWithExtension()
    {
        $this->expectException(EntrypointNotFoundException::class);
        $this->filesystem->method('exists')->willReturn(true);
        $entrypoints = [
            'app' => [
                'js' => [
                    'file.js'
                ]
            ],
        ];
        $this->jsonDecoder->method('decode')->willReturn(['entrypoints' => $entrypoints]);
        $this->assertEmpty($this->subject->getJavaScriptFiles('app.js'));
    }

    /**
     * @test
     */
    public function throwsExceptionIfEntrypointsFileDoesNotExist()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->filesystem->method('exists')->willReturn(false);
        $this->assertEmpty($this->subject->getJavaScriptFiles('foo'));
    }

    /**
     * @test
     */
    public function throwsExceptionIfJsonCanNotBeRetrieved()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->filesystem->method('exists')->willReturn(true);
        $this->assertEmpty($this->subject->getJavaScriptFiles('foo'));
    }

    /**
     * @test
     */
    public function throwsExceptionOnMissingEntrypoint()
    {
        $this->expectException(EntrypointNotFoundException::class);
        $this->filesystem->method('exists')->willReturn(true);
        $entrypoints = [
            'app' => [
                'js' => [
                    'file.js'
                ]
            ],
        ];
        $this->jsonDecoder->method('decode')->willReturn(['entrypoints' => $entrypoints]);
        $this->assertEmpty($this->subject->getJavaScriptFiles('doesnotexist'));
    }

    /**
     * @test
     */
    public function getJsFiles()
    {
        $this->filesystem->method('exists')->willReturn(true);
        $entrypoints = [
            'app' => [
                'js' => [
                    'file.js'
                ]
            ],
        ];
        $this->jsonDecoder->method('decode')->willReturn(['entrypoints' => $entrypoints]);
        $this->assertContains('file.js', $this->subject->getJavaScriptFiles('app'));
    }

    /**
     * @test
     */
    public function reset()
    {
        $this->assertNull($this->subject->reset());
    }
}
