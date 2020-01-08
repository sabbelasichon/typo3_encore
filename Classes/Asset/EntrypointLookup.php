<?php
declare(strict_types = 1);

namespace Ssch\Typo3Encore\Asset;

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

use InvalidArgumentException;
use Ssch\Typo3Encore\Integration\CacheFactory;
use Ssch\Typo3Encore\Integration\FilesystemInterface;
use Ssch\Typo3Encore\Integration\JsonDecoderInterface;
use TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Cache\Frontend\NullFrontend;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class EntrypointLookup implements EntrypointLookupInterface, IntegrityDataProviderInterface
{

    /**
     * @var array
     */
    private $entriesData;

    /**
     * @var string
     */
    private $entrypointJsonPath;

    /**
     * @var array
     */
    private $returnedFiles = [];

    /**
     * @var JsonDecoderInterface
     */
    private $jsonDecoder;

    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    /**
     * @var FrontendInterface
     */
    private $cache;

    /**
     * @var string
     */
    private $cacheKey;

    public function __construct(string $entrypointJsonPath, string $cacheKeyPrefix, JsonDecoderInterface $jsonDecoder, FilesystemInterface $filesystem, CacheFactory $cacheFactory)
    {
        $this->entrypointJsonPath = $filesystem->getFileAbsFileName($entrypointJsonPath);
        $this->jsonDecoder = $jsonDecoder;
        $this->filesystem = $filesystem;
        $this->cache = $cacheFactory->createInstance();
        $this->cacheKey = sprintf('%s-%s', $cacheKeyPrefix, CacheFactory::CACHE_KEY);
    }

    /**
     * @param string $entryName
     *
     * @return array
     */
    public function getJavaScriptFiles(string $entryName): array
    {
        return $this->getEntryFiles($entryName, 'js');
    }

    /**
     * @param string $entryName
     *
     * @return array
     */
    public function getCssFiles(string $entryName): array
    {
        return $this->getEntryFiles($entryName, 'css');
    }

    public function getIntegrityData(): array
    {
        $entriesData = $this->getEntriesData();

        if (! array_key_exists('integrity', $entriesData)) {
            return [];
        }

        return $entriesData['integrity'];
    }

    /**
     * Resets the state of this service.
     */
    public function reset()
    {
        $this->returnedFiles = [];
    }

    private function getEntryFiles(string $entryName, string $key): array
    {
        $this->validateEntryName($entryName);
        $entriesData = $this->getEntriesData();
        $entryData = $entriesData['entrypoints'][$entryName];

        if (! isset($entryData[$key])) {
            // If we don't find the file type then just send back nothing.
            return [];
        }

        // make sure to not return the same file multiple times
        $entryFiles = $entryData[$key];
        $newFiles = array_values(array_diff($entryFiles, $this->returnedFiles));
        $this->returnedFiles = array_merge($this->returnedFiles, $newFiles);

        return $newFiles;
    }

    private function validateEntryName(string $entryName)
    {
        $entriesData = $this->getEntriesData();
        if (! isset($entriesData['entrypoints'][$entryName])) {
            $withoutExtension = substr($entryName, 0, (int)strrpos($entryName, '.'));

            if (isset($entriesData['entrypoints'][$withoutExtension])) {
                throw new EntrypointNotFoundException(sprintf('Could not find the entry "%s". Try "%s" instead (without the extension).', $entryName, $withoutExtension));
            }

            throw new EntrypointNotFoundException(sprintf('Could not find the entry "%s" in "%s". Found: %s.', $entryName, $this->entrypointJsonPath, implode(', ', array_keys($entriesData))));
        }
    }

    private function getEntriesData(): array
    {
        if (null !== $this->entriesData) {
            return $this->entriesData;
        }

        if ($this->cache && $this->cache->has($this->cacheKey)) {
            return $this->cache->get($this->cacheKey);
        }

        if (! $this->filesystem->exists($this->entrypointJsonPath)) {
            throw new InvalidArgumentException(sprintf('Could not find the entrypoints file from Webpack: the file "%s" does not exist.', $this->entrypointJsonPath));
        }

        $this->entriesData = $this->jsonDecoder->decode($this->filesystem->get($this->entrypointJsonPath));

        if (null === $this->entriesData) {
            throw new InvalidArgumentException(sprintf('There was a problem JSON decoding the "%s" file', $this->entrypointJsonPath));
        }

        if (! isset($this->entriesData['entrypoints'])) {
            throw new InvalidArgumentException(sprintf('Could not find an "entrypoints" key in the "%s" file', $this->entrypointJsonPath));
        }

        if ($this->cache) {
            $this->cache->set($this->cacheKey, $this->entriesData);
        }

        return $this->entriesData;
    }
}
