<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Tests\Unit\Integration;

use PHPUnit\Framework\MockObject\MockObject;
use Ssch\Typo3Encore\Integration\CacheFactory;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class CacheFactoryTest extends UnitTestCase
{
    protected CacheFactory $subject;

    /**
     * @var CacheManager|MockObject
     */
    protected $cacheManager;

    protected function setUp(): void
    {
        $this->cacheManager = $this->getMockBuilder(CacheManager::class)->disableOriginalConstructor()->getMock();
        $this->subject = new CacheFactory($this->cacheManager);
    }

    public function testNullFrontendIsReturnedBecauseNoSuchCacheExceptionIsThrown(): void
    {
        $this->cacheManager->expects(self::once())->method('getCache')->willThrowException(new NoSuchCacheException());
        self::assertNull($this->subject->createInstance());
    }

    public function testDefinedCacheIsReturned(): void
    {
        $cache = $this->getMockBuilder(FrontendInterface::class)->getMock();
        $this->cacheManager->expects(self::once())->method('getCache')->willReturn($cache);
        self::assertSame($cache, $this->subject->createInstance());
    }
}
