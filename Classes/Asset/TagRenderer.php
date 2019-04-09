<?php
declare(strict_types=1);


namespace Ssch\Typo3Encore\Asset;


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