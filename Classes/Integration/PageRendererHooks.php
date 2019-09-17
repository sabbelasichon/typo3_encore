<?php
declare(strict_types=1);

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

use Ssch\Typo3Encore\Asset\EntrypointLookupCollectionInterface;
use Ssch\Typo3Encore\Asset\EntrypointLookupInterface;
use Ssch\Typo3Encore\Asset\IntegrityDataProviderInterface;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

final class PageRendererHooks
{
    const ENCORE_PREFIX = 'typo3_encore:';

    /**
     * @var EntrypointLookupCollectionInterface|object
     */
    private $entrypointLookupCollection;

    /**
     * PageRendererHooks constructor.
     *
     * @param EntrypointLookupCollectionInterface|object|null $entrypointLookupCollection
     */
    public function __construct(EntrypointLookupCollectionInterface $entrypointLookupCollection = null)
    {
        if ( ! $entrypointLookupCollection instanceof EntrypointLookupCollectionInterface) {
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            $entrypointLookupCollection = $objectManager->get(EntrypointLookupCollectionInterface::class);
        }
        $this->entrypointLookupCollection = $entrypointLookupCollection;
    }

    public function renderPreProcess(array $params, PageRenderer $pageRenderer)
    {
        // Add JavaScript Files by entryNames
        foreach (['jsFiles', 'jsFooterLibs', 'jsLibs'] as $includeType) {
            if ( ! empty($params[$includeType])) {
                foreach ($params[$includeType] as $key => $jsFile) {
                    if ($this->isEncoreEntryName($jsFile['file'])) {
                        $entryPointLookup = $this->getEntrypointLookup($jsFile['file']);

                        $integrityHashes = ($entryPointLookup instanceof IntegrityDataProviderInterface) ? $entryPointLookup->getIntegrityData() : [];

                        unset($params[$includeType][$key]);

                        $attributes = $jsFile;
                        foreach ($entryPointLookup->getJavaScriptFiles($this->resolveEntryName($jsFile['file'])) as $file) {
                            $attributes['file'] = $file;
                            $attributes['integrity'] = $integrityHashes[$file] ?? null;
                            $params[$includeType][$file] = $attributes;
                        }
                    }
                }
            }
        }

        // Add CSS-Files by entryNames
        foreach (['cssFiles'] as $includeType) {
            if ( ! empty($params[$includeType])) {
                foreach ($params[$includeType] as $key => $cssFile) {
                    if ($this->isEncoreEntryName($cssFile['file'])) {
                        $entryPointLookup = $this->getEntrypointLookup($cssFile['file']);

                        unset($params['cssFiles'][$key]);

                        $attributes = $cssFile;
                        foreach ($entryPointLookup->getCssFiles($this->resolveEntryName($cssFile['file'])) as $file) {
                            $attributes['file'] = $file;
                            $params['cssFiles'][$file] = $attributes;
                        }
                    }
                }
            }
        }
    }

    /**
     * @param string $file
     *
     * @return bool
     */
    private function isEncoreEntryName(string $file): bool
    {
        return StringUtility::beginsWith($file, self::ENCORE_PREFIX);
    }

    /**
     * @param string $file
     *
     * @return string
     */
    private function removePrefix(string $file): string
    {
        return str_replace(self::ENCORE_PREFIX, '', $file);
    }

    /**
     * @param string $file
     *
     * @return string
     */
    private function resolveEntryName(string $file): string
    {
        list($buildName, $entryName) = $this->createBuildAndEntryName($file);
        return $entryName ?? $buildName;
    }

    /**
     * @param string $file
     *
     * @return EntrypointLookupInterface
     */
    private function getEntrypointLookup(string $file): EntrypointLookupInterface
    {
        list($buildName, $entryName) = $this->createBuildAndEntryName($file);
        $buildName = $entryName ? $buildName : '_default';

        return $this->entrypointLookupCollection->getEntrypointLookup($buildName);
    }

    /**
     * @param string $file
     *
     * @return array
     */
    private function createBuildAndEntryName(string $file): array
    {
        return GeneralUtility::trimExplode(':', $this->removePrefix($file), 2);
    }
}
