<?php

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Tests\Unit\Asset;

/**
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Ssch\Typo3Encore\Asset\EntrypointLookupCollection;
use Ssch\Typo3Encore\Asset\EntrypointLookupInterface;
use Ssch\Typo3Encore\Asset\UndefinedBuildException;
use Ssch\Typo3Encore\Integration\EntryLookupFactoryInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \Ssch\Typo3Encore\Asset\EntrypointLookupCollection
 */
final class EntrypointLookupCollectionTest extends UnitTestCase
{
    protected EntrypointLookupCollection $subject;

    /**
     * @var EntryLookupFactoryInterface
     */
    protected $entryLookupFactory;

    protected function setUp(): void
    {
        $this->entryLookupFactory = $this->getMockBuilder(EntryLookupFactoryInterface::class)->getMock();
        $buildEntrypoints = [
            'existing' => $this->getMockBuilder(EntrypointLookupInterface::class)->getMock()
        ];
        $this->entryLookupFactory->method('getCollection')->willReturn($buildEntrypoints);
        $this->subject = new EntrypointLookupCollection($this->entryLookupFactory);
    }

    /**
     * @test
     * @throws UndefinedBuildException
     */
    public function getEntrypointLookupWithoutDefaultBuildNameThrowsException(): void
    {
        $this->expectException(UndefinedBuildException::class);
        $this->subject->getEntrypointLookup();
    }

    /**
     * @test
     * @throws UndefinedBuildException
     */
    public function getEntrypointLookupWithWrongBuildNameThrowsException(): void
    {
        $this->expectException(UndefinedBuildException::class);
        $this->subject->getEntrypointLookup('nonexisting');
    }

    /**
     * @test
     * @testdox Get defined EntryPointLookup instance successfully
     * @throws UndefinedBuildException
     */
    public function getEntrypointLookup(): void
    {
        self::assertInstanceOf(EntrypointLookupInterface::class, $this->subject->getEntrypointLookup('existing'));
    }

    /**
     * @test
     * @throws UndefinedBuildException
     */
    public function getEntrypointLookupWithDefinedDefaultBuild(): void
    {
        $subject = new EntrypointLookupCollection($this->entryLookupFactory, 'existing');
        self::assertInstanceOf(EntrypointLookupInterface::class, $subject->getEntrypointLookup());
    }
}
