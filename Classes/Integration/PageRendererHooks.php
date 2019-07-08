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
     * @var EntrypointLookupInterface|object
     */
    private $entrypointLookup;

    /**
     * PageRendererHooks constructor.
     *
     * @param EntrypointLookupInterface|object|null $entrypointLookup
     */
    public function __construct(EntrypointLookupInterface $entrypointLookup = null)
    {
        if (! $entrypointLookup instanceof EntrypointLookupInterface) {
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            $entrypointLookup = $objectManager->get(EntrypointLookupInterface::class);
        }
        $this->entrypointLookup = $entrypointLookup;
    }

    public function renderPreProcess(array $params, PageRenderer $pageRenderer)
    {
        // Add JavaScript Files by entryNames
        foreach (['jsFiles', 'jsFooterLibs', 'jsLibs'] as $includeType) {
            if (! empty($params[$includeType])) {
                $integrityHashes = ($this->entrypointLookup instanceof IntegrityDataProviderInterface) ? $this->entrypointLookup->getIntegrityData() : [];
                foreach ($params[$includeType] as $key => $jsFile) {
                    if ($this->isEncoreEntryName($jsFile['file'])) {
                        unset($params[$includeType][$key]);

                        $attributes = $jsFile;
                        foreach ($this->entrypointLookup->getJavaScriptFiles($this->resolveEntryName($jsFile['file'])) as $file) {
                            $attributes['file'] = $file;
                            $attributes['integrity'] = $integrityHashes[$file] ?? null;
                            $params[$includeType][$file] = $attributes;
                        }
                    }
                }
            }
        }

        // Add CSS-Files by entryNames
        foreach ($params['cssFiles'] as $key => $cssFile) {
            if ($this->isEncoreEntryName($cssFile['file'])) {
                unset($params['cssFiles'][$key]);

                $attributes = $cssFile;
                foreach ($this->entrypointLookup->getCssFiles($this->resolveEntryName($cssFile['file'])) as $file) {
                    $attributes['file'] = $file;
                    $params['cssFiles'][$file] = $attributes;
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
    private function resolveEntryName(string $file): string
    {
        return str_replace(self::ENCORE_PREFIX, '', $file);
    }
}
