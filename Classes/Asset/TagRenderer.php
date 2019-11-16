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

use Ssch\Typo3Encore\Integration\AssetRegistryInterface;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class TagRenderer implements TagRendererInterface
{
    /**
     * @var EntrypointLookupCollectionInterface
     */
    private $entrypointLookupCollection;

    /**
     * @var array
     */
    private $defaultAttributes = [];

    /**
     * @var array
     */
    private $renderedFiles = [];

    /**
     * @var AssetRegistryInterface
     */
    private $assetRegistry;

    public function __construct(EntrypointLookupCollectionInterface $entrypointLookupCollection, AssetRegistryInterface $assetRegistry)
    {
        $this->entrypointLookupCollection = $entrypointLookupCollection;
        $this->assetRegistry = $assetRegistry;
    }

    /**
     * @param string $entryName
     * @param string $position
     * @param string $buildName
     * @param object|PageRenderer|null $pageRenderer
     * @param array $parameters
     */
    public function renderWebpackScriptTags(string $entryName, string $position = 'footer', $buildName = '_default', PageRenderer $pageRenderer = null, array $parameters = [])
    {
        $pageRenderer = $pageRenderer ?? GeneralUtility::makeInstance(PageRenderer::class);
        $entryPointLookup = $this->getEntrypointLookup($buildName);

        $integrityHashes = ($entryPointLookup instanceof IntegrityDataProviderInterface) ? $entryPointLookup->getIntegrityData() : [];
        $files = $entryPointLookup->getJavaScriptFiles($entryName);

        unset($parameters['file']);
        foreach ($files as $file) {
            $attributes = [
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
                'crossorigin' => ''
            ];

            $attributes = array_values(array_replace($attributes, $parameters));

            if ($position === 'footer') {
                $pageRenderer->addJsFooterFile(...$attributes);
            } else {
                $pageRenderer->addJsFile(...$attributes);
            }
            $this->assetRegistry->registerFile($file, 'script');
        }
    }

    /**
     * @param string $entryName
     * @param string $media
     * @param string $buildName
     * @param object|PageRenderer|null $pageRenderer
     * @param array $parameters
     */
    public function renderWebpackLinkTags(string $entryName, string $media = 'all', $buildName = '_default', PageRenderer $pageRenderer = null, array $parameters = [])
    {
        $pageRenderer = $pageRenderer ?? GeneralUtility::makeInstance(PageRenderer::class);
        $entryPointLookup = $this->getEntrypointLookup($buildName);
        $files = $entryPointLookup->getCssFiles($entryName);

        unset($parameters['file']);
        foreach ($files as $file) {
            $attributes = [
                'file' => $file,
                'rel' => 'stylesheet',
                'media' => $media,
                'title' => '',
                'compress' => false,
                'forceOnTop' => false,
                'allWrap' => '',
                'excludeFromConcatenation' => true,
                'splitChar' => '|',
                'inline' => false
            ];

            $attributes = array_values(array_replace($attributes, $parameters));

            $pageRenderer->addCssFile(...$attributes);
            $this->assetRegistry->registerFile($file, 'style');
        }
    }

    private function getEntrypointLookup(string $buildName): EntrypointLookupInterface
    {
        return $this->entrypointLookupCollection->getEntrypointLookup($buildName);
    }
}
