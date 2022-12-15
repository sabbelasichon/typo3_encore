<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\ViewHelpers;

use Ssch\Typo3Encore\Asset\EntrypointLookupCollectionInterface;
use Ssch\Typo3Encore\Asset\EntrypointLookupInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

final class WebpackJsFilesViewHelper extends AbstractViewHelper
{
    private EntrypointLookupCollectionInterface $entrypointLookupCollection;

    public function __construct(EntrypointLookupCollectionInterface $entrypointLookupCollection)
    {
        $this->entrypointLookupCollection = $entrypointLookupCollection;
    }

    public function initializeArguments(): void
    {
        $this->registerArgument('entryName', 'string', 'The entry name', true);
        $this->registerArgument(
            'buildName',
            'string',
            'The build name',
            false,
            EntrypointLookupInterface::DEFAULT_BUILD
        );
    }

    public function render(): array
    {
        return $this->entrypointLookupCollection->getEntrypointLookup(
            $this->arguments['buildName']
        )->getJavaScriptFiles($this->arguments['entryName']);
    }
}
