<?php
declare(strict_types=1);


namespace Ssch\Typo3Encore\Integration;


use TYPO3\CMS\Core\Cache\Backend\NullBackend;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class CacheFactory
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