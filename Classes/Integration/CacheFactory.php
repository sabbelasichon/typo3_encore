<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Integration;

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;

/**
 * @final
 */
class CacheFactory
{
    /**
     * @var string
     */
    public const CACHE_KEY = 'typo3_encore';

    protected CacheManager $cacheManager;

    public function __construct(CacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
    }

    /**
     * @return FrontendInterface
     */
    public function createInstance(): ?FrontendInterface
    {
        try {
            return $this->cacheManager->getCache(self::CACHE_KEY);
        } catch (NoSuchCacheException $e) {
            return null;
        }
    }
}
