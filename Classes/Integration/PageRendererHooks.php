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

use Ssch\Typo3Encore\Asset\TagRendererInterface;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

final class PageRendererHooks
{
    private const ENCORE_PREFIX = 'typo3_encore:';

    /**
     * @var TagRendererInterface
     */
    private $tagRenderer;

    public function __construct(TagRendererInterface $tagRenderer = null)
    {
        if (! $tagRenderer instanceof TagRendererInterface) {
            // @codeCoverageIgnoreStart
            /** @var ObjectManagerInterface $objectManager */
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            /** @var TagRendererInterface $tagRenderer */
            $tagRenderer = $objectManager->get(TagRendererInterface::class);
            // @codeCoverageIgnoreEnd
        }
        $this->tagRenderer = $tagRenderer;
    }

    public function renderPreProcess(array $params, PageRenderer $pageRenderer): void
    {
        // Add JavaScript Files by entryNames
        foreach (['jsFiles', 'jsFooterLibs', 'jsLibs'] as $includeType) {
            if (! empty($params[$includeType])) {
                foreach ($params[$includeType] as $key => $jsFile) {
                    if ($this->isEncoreEntryName($jsFile['file'])) {
                        [$first, $second] = $this->createBuildAndEntryName($jsFile['file']);

                        $buildName = $second ? $first : '_default';
                        $entryName = $second ?? $first;

                        $position = (int)$params[$includeType][$key]['section'] === PageRenderer::PART_FOOTER ? 'footer' : '';

                        unset($params[$includeType][$key], $jsFile['file'], $jsFile['section']);

                        $this->tagRenderer->renderWebpackScriptTags($entryName, $position, $buildName, $pageRenderer, $jsFile);
                    }
                }
            }
        }

        // Add CSS-Files by entryNames
        foreach (['cssFiles'] as $includeType) {
            if (! empty($params[$includeType])) {
                foreach ($params[$includeType] as $key => $cssFile) {
                    if ($this->isEncoreEntryName($cssFile['file'])) {
                        [$first, $second] = $this->createBuildAndEntryName($cssFile['file']);

                        $buildName = $second ? $first : '_default';
                        $entryName = $second ?? $first;

                        unset($params[$includeType][$key], $cssFile['file']);

                        $this->tagRenderer->renderWebpackLinkTags($entryName, 'all', $buildName, $pageRenderer, $cssFile);
                    }
                }
            }
        }
    }

    private function isEncoreEntryName(string $file): bool
    {
        return StringUtility::beginsWith($file, self::ENCORE_PREFIX);
    }

    private function removePrefix(string $file): string
    {
        return str_replace(self::ENCORE_PREFIX, '', $file);
    }

    private function createBuildAndEntryName(string $file): array
    {
        return GeneralUtility::trimExplode(':', $this->removePrefix($file), 2);
    }
}
