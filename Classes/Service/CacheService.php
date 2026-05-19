<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use TYPO3\CMS\Core\Cache\CacheDataCollector;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Frontend\Event\BeforePageCacheIdentifierIsHashedEvent;

final readonly class CacheService
{
    private const CACHE_KEY_IDENTIFER = 'typo3-encore-page-cache-identifier';

    private const CACHE_KEY_SUFFIX = '-typo3-encore';

    public function __construct(
        #[Autowire(service: 'cache.pages')]
        private FrontendInterface $pageCache,
        #[Autowire(service: 'cache.runtime')]
        private FrontendInterface $runtimeCache,
    ) {
    }

    /**
     * Needed for TYPO3 13. Method CacheDataCollector::getPageCacheIdentifier not available
     */
    public function storePageCacheIdentifier(BeforePageCacheIdentifierIsHashedEvent $event): void
    {
        $parameters = $event->getPageCacheIdentifierParameters();
        $identifier = $parameters['id'] . '_' . hash('xxh3', serialize($parameters));
        $this->runtimeCache->set(self::CACHE_KEY_IDENTIFER, $identifier);
    }

    public function set(CacheDataCollector $cacheDataCollector, array $data, array $tags, int $lifetime): void
    {
        $entryIdentifier = $this->getPageCacheIdentifier($cacheDataCollector);
        if ('' === $entryIdentifier) {
            return;
        }
        $this->pageCache->set($entryIdentifier . self::CACHE_KEY_SUFFIX, $data, $tags, $lifetime);
    }

    public function get(CacheDataCollector $cacheDataCollector): array
    {
        $entryIdentifier = $this->getPageCacheIdentifier($cacheDataCollector);
        if ('' === $entryIdentifier) {
            return [];
        }
        $entry = $this->pageCache->get($entryIdentifier . self::CACHE_KEY_SUFFIX);
        if (! is_array($entry)) {
            $entry = [];
        }
        return $entry;
    }

    private function getPageCacheIdentifier(CacheDataCollector $cacheDataCollector): string
    {
        // @phpstan-ignore-next-line - method exists in TYPO3 14 but not in TYPO3 13
        if (method_exists($cacheDataCollector, 'getPageCacheIdentifier')) {
            return $cacheDataCollector->getPageCacheIdentifier();
        }
        return (string) $this->runtimeCache->get(self::CACHE_KEY_IDENTIFER);
    }
}
