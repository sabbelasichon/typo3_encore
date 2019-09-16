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

use TYPO3\CMS\Core\Page\PageRenderer;

final class TagRenderer implements TagRendererInterface
{
    /**
     * @var PageRenderer
     */
    private $pageRenderer;

    /**
     * @var EntrypointLookupCollectionInterface
     */
    private $entrypointLookupCollection;

    public function __construct(PageRenderer $pageRenderer, EntrypointLookupCollectionInterface $entrypointLookupCollection)
    {
        $this->pageRenderer = $pageRenderer;
        $this->entrypointLookupCollection = $entrypointLookupCollection;
    }

    public function renderWebpackScriptTags(string $entryName, string $position = 'footer', $buildName = '_default')
    {
        $entryPointLookup = $this->getEntrypointLookup($buildName);

        $integrityHashes = ($entryPointLookup instanceof IntegrityDataProviderInterface) ? $entryPointLookup->getIntegrityData() : [];
        $files = $entryPointLookup->getJavaScriptFiles($entryName);

        foreach ($files as $file) {
            $attributes = [
                $file,
                'text/javascript',
                false,
                false,
                '',
                true,
                '|',
                false,
                $integrityHashes[$file] ?? '',
            ];

            if ($position === 'footer') {
                $this->pageRenderer->addJsFooterFile(...$attributes);
            } else {
                $this->pageRenderer->addJsFile(...$attributes);
            }
        }
    }

    public function renderWebpackLinkTags(string $entryName, string $media = 'all', $buildName = '_default')
    {
        $entryPointLookup = $this->getEntrypointLookup($buildName);
        $files = $entryPointLookup->getCssFiles($entryName);

        foreach ($files as $file) {
            $attributes = [
                $file,
                'stylesheet',
                $media,
                '',
                false,
                false,
                '',
                false,
            ];
            $this->pageRenderer->addCssFile(...$attributes);
        }
    }

    private function getEntrypointLookup(string $buildName): EntrypointLookupInterface
    {
        return $this->entrypointLookupCollection->getEntrypointLookup($buildName);
    }
}
