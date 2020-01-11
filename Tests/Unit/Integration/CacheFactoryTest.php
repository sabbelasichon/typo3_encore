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

use Nimut\TestingFramework\TestCase\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Ssch\Typo3Encore\Integration\CacheFactory;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;

/**
 * @covers \Ssch\Typo3Encore\Integration\CacheFactory
 */
class CacheFactoryTest extends UnitTestCase
{
    /**
     * @var CacheFactory
     */
    protected $subject;

    /**
     * @var CacheManager|MockObject
     */
    private $cacheManager;

    protected function setUp()
    {
        $this->cacheManager = $this->getMockBuilder(CacheManager::class)->disableOriginalConstructor()->getMock();
        $this->subject = new CacheFactory($this->cacheManager);
    }

    /**
     * @test
     */
    public function nullFrontendIsReturnedBecauseNoSuchCacheExceptionIsThrown()
    {
        $this->cacheManager->expects($this->once())->method('getCache')->willThrowException(new NoSuchCacheException());
        $this->assertNull($this->subject->createInstance());
    }

    /**
     * @test
     */
    public function definedCacheIsReturned()
    {
        $cache = $this->getMockBuilder(FrontendInterface::class)->getMock();
        $this->cacheManager->expects($this->once())->method('getCache')->willReturn($cache);
        $this->assertSame($cache, $this->subject->createInstance());
    }
}
