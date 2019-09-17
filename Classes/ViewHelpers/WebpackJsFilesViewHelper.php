<?php
declare(strict_types = 1);

namespace Ssch\Typo3Encore\ViewHelpers;

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
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

final class WebpackJsFilesViewHelper extends AbstractViewHelper
{

    /**
     * @var EntrypointLookupCollectionInterface
     */
    private $entrypointLookupCollection;

    public function __construct(EntrypointLookupCollectionInterface $entrypointLookupCollection)
    {
        $this->entrypointLookupCollection = $entrypointLookupCollection;
    }

    public function initializeArguments()
    {
        $this->registerArgument('entryName', 'string', 'The entry name', true);
        $this->registerArgument('entrypointName', 'string', 'The build name', false, '_default');
    }

    public function render(): array
    {
        $entryPointLookup = $this->entrypointLookupCollection->getEntrypointLookup($this->arguments['entrypointName']);
        return $entryPointLookup->getJavaScriptFiles($this->arguments['entryName']);
    }
}
