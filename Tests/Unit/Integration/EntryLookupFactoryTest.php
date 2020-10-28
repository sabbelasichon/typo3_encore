<?php

namespace Ssch\Typo3Encore\Tests\Unit\Integration;

/**
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use PHPUnit\Framework\MockObject\MockObject;
use Ssch\Typo3Encore\Asset\EntrypointLookupInterface;
use Ssch\Typo3Encore\Integration\EntryLookupFactory;
use Ssch\Typo3Encore\Integration\FilesystemInterface;
use Ssch\Typo3Encore\Integration\SettingsServiceInterface;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \Ssch\Typo3Encore\Integration\EntryLookupFactory
 */
final class EntryLookupFactoryTest extends UnitTestCase
{
    /**
     * @var EntryLookupFactory
     */
    protected $subject;

    /**
     * @var SettingsServiceInterface
     */
    protected $settingsService;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var FilesystemInterface|MockObject
     */
    protected $filesystem;

    protected function setUp(): void
    {
        $this->objectManager = $this->getMockBuilder(ObjectManagerInterface::class)->getMock();
        $this->settingsService = $this->getMockBuilder(SettingsServiceInterface::class)->getMock();
        $this->filesystem = $this->getMockBuilder(FilesystemInterface::class)->getMock();
        $this->subject = new EntryLookupFactory($this->settingsService, $this->objectManager, $this->filesystem);
    }

    /**
     * @test
     */
    public function getCollectionWithDefaultCollection(): void
    {
        $builds = [
            'config1' => 'path/to/config1'
        ];

        $this->settingsService->expects($this->at(0))->method('getArrayByPath')->with($this->equalTo('builds'))->willReturn($builds);
        $this->settingsService->expects($this->at(1))->method('getStringByPath')->with($this->equalTo('entrypointJsonPath'))->willReturn('path/to/entrypoints.json');

        $this->objectManager->expects($this->at(0))->method('get')->willReturn($this->getMockBuilder(EntrypointLookupInterface::class)->getMock());
        $this->objectManager->expects($this->at(1))->method('get')->willReturn($this->getMockBuilder(EntrypointLookupInterface::class)->getMock());

        $this->filesystem->method('exists')->willReturn(true);

        $collection = $this->subject->getCollection();

        $this->assertContainsOnlyInstancesOf(EntrypointLookupInterface::class, $collection);
        $this->assertArrayHasKey('_default', $collection);

        $this->assertSame($collection, $this->subject->getCollection());
    }
}
