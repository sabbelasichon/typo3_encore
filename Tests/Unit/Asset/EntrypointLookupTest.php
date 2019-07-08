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

use Nimut\TestingFramework\TestCase\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Ssch\Typo3Encore\Asset\EntrypointLookup;
use Ssch\Typo3Encore\Integration\CacheFactory;
use Ssch\Typo3Encore\Integration\FilesystemInterface;
use Ssch\Typo3Encore\Integration\JsonDecoderInterface;
use Ssch\Typo3Encore\Integration\SettingsServiceInterface;

class EntrypointLookupTest extends UnitTestCase
{
    private $subject;

    /**
     * @var MockObject|SettingsServiceInterface
     */
    private $settingsService;

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

    protected function setUp()
    {
        $this->settingsService = $this->getMockBuilder(SettingsServiceInterface::class)->getMock();
        $this->jsonDecoder = $this->getMockBuilder(JsonDecoderInterface::class)->getMock();
        $this->filesystem = $this->getMockBuilder(FilesystemInterface::class)->getMock();
        $this->cacheFactory = $this->getMockBuilder(CacheFactory::class)->disableOriginalConstructor()->getMock();

        $this->subject = new EntrypointLookup($this->settingsService, $this->jsonDecoder, $this->filesystem, $this->cacheFactory);
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
}
