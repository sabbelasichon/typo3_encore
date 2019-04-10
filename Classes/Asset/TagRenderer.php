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

final class TagRenderer
{
    /**
     * @var PageRenderer
     */
    private $pageRenderer;

    /**
     * @var EntrypointLookupInterface
     */
    private $entrypointLookup;

    /**
     * @var array|string[]
     */
    private $integrityHashes;

    /**
     * TagRenderer constructor.
     *
     * @param PageRenderer $pageRenderer
     * @param EntrypointLookupInterface $entrypointLookup
     */
    public function __construct(PageRenderer $pageRenderer, EntrypointLookupInterface $entrypointLookup)
    {
        $this->pageRenderer = $pageRenderer;
        $this->entrypointLookup = $entrypointLookup;
        $this->integrityHashes = ($this->entrypointLookup instanceof IntegrityDataProviderInterface) ? $this->entrypointLookup->getIntegrityData() : [];
    }

    public function renderWebpackScriptTags(string $entryName, string $position = 'footer'): void
    {
        $files = $this->entrypointLookup->getJavaScriptFiles($entryName);

        foreach ($files as $file) {
            $attributes = [
                $file,
                'text/javascript',
                true,
                false,
                '',
                false,
                '|',
                false,
                $this->integrityHashes[$file] ?? '',
            ];

            if ($position === 'footer') {
                $this->pageRenderer->addJsFooterFile(...$attributes);
            } else {
                $this->pageRenderer->addJsFile(...$attributes);
            }
        }
    }

    public function renderWebpackLinkTags(string $entryName): void
    {
        $files = $this->entrypointLookup->getCssFiles($entryName);

        foreach ($files as $file) {
            $this->pageRenderer->addCssFile($file);
        }
    }
}
