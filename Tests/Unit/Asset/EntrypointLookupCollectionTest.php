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

use PHPUnit\Framework\TestCase;
use Ssch\Typo3Encore\Asset\EntrypointLookupCollection;
use Ssch\Typo3Encore\Asset\EntrypointLookupInterface;
use Ssch\Typo3Encore\Asset\UndefinedBuildException;
use Ssch\Typo3Encore\Integration\EntryLookupFactoryInterface;

/**
 * @covers \Ssch\Typo3Encore\Asset\EntrypointLookupCollection
 */
class EntrypointLookupCollectionTest extends TestCase
{
    /**
     * @var EntrypointLookupCollection
     */
    protected $subject;

    /**
     * @var EntryLookupFactoryInterface
     */
    protected $entryLookupFactory;

    protected function setUp()
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
    public function getEntrypointLookupWithoutDefaultBuildNameThrowsException()
    {
        $this->expectException(UndefinedBuildException::class);
        $this->subject->getEntrypointLookup();
    }

    /**
     * @test
     * @throws UndefinedBuildException
     */
    public function getEntrypointLookupWithWrongBuildNameThrowsException()
    {
        $this->expectException(UndefinedBuildException::class);
        $this->subject->getEntrypointLookup('nonexisting');
    }

    /**
     * @test
     * @testdox Get defined EntryPointLookup instance successfully
     * @throws UndefinedBuildException
     */
    public function getEntrypointLookup()
    {
        $this->assertInstanceOf(EntrypointLookupInterface::class, $this->subject->getEntrypointLookup('existing'));
    }

    /**
     * @test
     * @throws UndefinedBuildException
     */
    public function getEntrypointLookupWithDefinedDefaultBuild()
    {
        $subject = new EntrypointLookupCollection($this->entryLookupFactory, 'existing');
        $this->assertInstanceOf(EntrypointLookupInterface::class, $subject->getEntrypointLookup());
    }
}
