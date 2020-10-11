<?php
declare(strict_types=1);

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

use Ssch\Typo3Encore\Integration\AssetRegistryInterface;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class TagRenderer implements TagRendererInterface
{
    /**
     * @var array
     */
    public const ALLOWED_JAVASCRIPT_POSITIONS_WITH_CORRESPONDING_PAGE_RENDERER_METHOD_CALL = [
        'jsFiles' => 'addJsFile',
        'jsFooterLibs' => 'addJsFooterLibrary',
        'jsLibs' => 'addJsLibrary',
        'jsFooterFiles' => 'addJsFooterFile',
    ];

    /**
     * @var array
     */
    public const ALLOWED_CSS_POSITIONS = [
        'cssFiles',
        'cssLibs'
    ];

    /**
     * @var string
     */
    public const POSITION_FOOTER = 'footer';

    /**
     * @var EntrypointLookupCollectionInterface
     */
    private $entrypointLookupCollection;

    /**
     * @var AssetRegistryInterface
     */
    private $assetRegistry;

    public function __construct(EntrypointLookupCollectionInterface $entrypointLookupCollection, AssetRegistryInterface $assetRegistry)
    {
        $this->entrypointLookupCollection = $entrypointLookupCollection;
        $this->assetRegistry = $assetRegistry;
    }

    public function renderWebpackScriptTags(string $entryName, string $position = self::POSITION_FOOTER, string $buildName = '_default', PageRenderer $pageRenderer = null, array $parameters = [], bool $registerFile = true): void
    {
        // Keep this for backwards compatibility
        $position = $position === self::POSITION_FOOTER ? 'jsFooterFiles' : $position;

        /** @var PageRenderer $pageRenderer */
        $pageRenderer = $pageRenderer ?? GeneralUtility::makeInstance(PageRenderer::class);
        $entryPointLookup = $this->getEntrypointLookup($buildName);

        $integrityHashes = ($entryPointLookup instanceof IntegrityDataProviderInterface) ? $entryPointLookup->getIntegrityData() : [];
        $files = $entryPointLookup->getJavaScriptFiles($entryName);

        unset($parameters['file']);
        foreach ($files as $file) {
            $attributes = array_replace([
                'file' => $file,
                'type' => 'text/javascript',
                'compress' => false,
                'forceOnTop' => false,
                'allWrap' => '',
                'excludeFromConcatenation' => true,
                'splitChar' => '|',
                'async' => false,
                'integrity' => $integrityHashes[$file] ?? '',
                'defer' => false,
                'crossorigin' => '',
            ], $parameters);

            $attributes = array_values($attributes);

            $pageRendererMethodCall = TagRenderer::ALLOWED_JAVASCRIPT_POSITIONS_WITH_CORRESPONDING_PAGE_RENDERER_METHOD_CALL['jsFiles'];

            if(array_key_exists($position, TagRenderer::ALLOWED_JAVASCRIPT_POSITIONS_WITH_CORRESPONDING_PAGE_RENDERER_METHOD_CALL)) {
                $pageRendererMethodCall = TagRenderer::ALLOWED_JAVASCRIPT_POSITIONS_WITH_CORRESPONDING_PAGE_RENDERER_METHOD_CALL[$position];
            }

            $pageRenderer->{$pageRendererMethodCall}(...$attributes);

            if ($registerFile === true) {
                $this->assetRegistry->registerFile($file, 'script');
            }
        }
    }

    public function renderWebpackLinkTags(string $entryName, string $media = 'all', string $buildName = '_default', PageRenderer $pageRenderer = null, array $parameters = [], bool $registerFile = true): void
    {
        /** @var PageRenderer $pageRenderer */
        $pageRenderer = $pageRenderer ?? GeneralUtility::makeInstance(PageRenderer::class);
        $entryPointLookup = $this->getEntrypointLookup($buildName);
        $files = $entryPointLookup->getCssFiles($entryName);

        unset($parameters['file']);
        foreach ($files as $file) {
            $attributes = array_replace([
                'file' => $file,
                'rel' => 'stylesheet',
                'media' => $media,
                'title' => '',
                'compress' => false,
                'forceOnTop' => false,
                'allWrap' => '',
                'excludeFromConcatenation' => true,
                'splitChar' => '|',
                'inline' => false,
            ], $parameters);

            $attributes = array_values($attributes);

            $pageRenderer->addCssFile(...$attributes);

            if ($registerFile === true) {
                $this->assetRegistry->registerFile($file, 'style');
            }
        }
    }

    private function getEntrypointLookup(string $buildName): EntrypointLookupInterface
    {
        return $this->entrypointLookupCollection->getEntrypointLookup($buildName);
    }
}
