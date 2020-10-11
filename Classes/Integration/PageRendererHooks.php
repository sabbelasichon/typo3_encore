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

use Ssch\Typo3Encore\Asset\TagRenderer;
use Ssch\Typo3Encore\Asset\TagRendererInterface;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

final class PageRendererHooks
{
    /**
     * @var string
     */
    private const ENCORE_PREFIX = 'typo3_encore:';

    /**
     * @var TagRendererInterface
     */
    private $tagRenderer;

    public function __construct(TagRendererInterface $tagRenderer = null)
    {
        if ( ! $tagRenderer instanceof TagRendererInterface) {
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
        $allowedJavascriptPositions = array_keys(TagRenderer::ALLOWED_JAVASCRIPT_POSITIONS_WITH_CORRESPONDING_PAGE_RENDERER_METHOD_CALL);
        foreach ($allowedJavascriptPositions as $includeType) {
            if (empty($params[$includeType])) {
                continue;
            }

            foreach ($params[$includeType] as $key => $jsFile) {
                if ( ! $this->isEncoreEntryName($jsFile['file'])) {
                    continue;
                }

                $buildAndEntryName = $this->createBuildAndEntryName($jsFile['file']);
                $buildName = '_default';

                if (count($buildAndEntryName) === 2) {
                    [$buildName, $entryName] = $buildAndEntryName;
                } else {
                    $entryName = $buildAndEntryName[0];
                }

                $position = '';
                if (array_key_exists('section', $params[$includeType][$key])) {
                    $position = (int)$params[$includeType][$key]['section'] === PageRenderer::PART_FOOTER ? TagRenderer::POSITION_FOOTER : '';
                }

                unset($params[$includeType][$key], $jsFile['file'], $jsFile['section'], $jsFile['integrity']);

                $this->tagRenderer->renderWebpackScriptTags($entryName, $position, $buildName, $pageRenderer, $jsFile);

            }
        }

        // Add CSS-Files by entryNames
        foreach (TagRenderer::ALLOWED_CSS_POSITIONS as $includeType) {
            if (empty($params[$includeType])) {
                continue;
            }

            foreach ($params[$includeType] as $key => $cssFile) {
                if ( ! $this->isEncoreEntryName($cssFile['file'])) {
                    continue;
                }
                $buildAndEntryName = $this->createBuildAndEntryName($cssFile['file']);
                $buildName = '_default';

                if (count($buildAndEntryName) === 2) {
                    [$buildName, $entryName] = $buildAndEntryName;
                } else {
                    $entryName = $buildAndEntryName[0];
                }

                unset($params[$includeType][$key], $cssFile['file']);

                $this->tagRenderer->renderWebpackLinkTags($entryName, 'all', $buildName, $pageRenderer, $cssFile);
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
        return GeneralUtility::trimExplode(':', $this->removePrefix($file), true, 2);
    }
}
