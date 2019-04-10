<?php
declare(strict_types = 1);

namespace Ssch\Typo3Encore\Integration;

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
    public const CACHE_KEY = 'typo3_encore_entrypoints';

    /**
     * @var CacheManager
     */
    protected $cacheManager;

    /**
     * CacheFactory constructor.
     *
     * @param CacheManager $cacheManager
     */
    public function __construct(CacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
    }

    /**
     * @return FrontendInterface
     * @throws NoSuchCacheException
     */
    public function createInstance(): FrontendInterface
    {
        return $this->cacheManager->getCache(self::CACHE_KEY);
    }
}
