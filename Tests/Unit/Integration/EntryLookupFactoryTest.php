<?php

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

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
    protected EntryLookupFactory $subject;

    /**
     * @var SettingsServiceInterface|MockObject
     */
    protected $settingsService;

    /**
     * @var ObjectManagerInterface|MockObject
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

        $this->settingsService->expects(self::atLeastOnce())->method('getArrayByPath')->with(self::equalTo('builds'))->willReturn($builds);
        $this->settingsService->expects(self::atLeastOnce())->method('getStringByPath')->with(self::equalTo('entrypointJsonPath'))->willReturn('path/to/entrypoints.json');
        $this->settingsService->expects(self::atLeastOnce())->method('getBooleanByPath')->with(self::equalTo('strictMode'))->willReturn(true);

        $this->objectManager->expects(self::atLeastOnce())->method('get')->willReturn($this->getMockBuilder(EntrypointLookupInterface::class)->getMock());
        $this->objectManager->expects(self::atLeastOnce())->method('get')->willReturn($this->getMockBuilder(EntrypointLookupInterface::class)->getMock());

        $this->filesystem->method('exists')->willReturn(true);

        $collection = $this->subject->getCollection();

        self::assertContainsOnlyInstancesOf(EntrypointLookupInterface::class, $collection);
        self::assertArrayHasKey('_default', $collection);

        self::assertSame($collection, $this->subject->getCollection());
    }
}
