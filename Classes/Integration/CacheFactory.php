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
use TYPO3\CMS\Core\Cache\Frontend\NullFrontend;
use TYPO3\CMS\Core\Cache\Frontend\VariableFrontend;

/**
 * @final
 */
class CacheFactory
{

    /**
     * @var string
     */
    const CACHE_KEY = 'typo3_encore';

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
