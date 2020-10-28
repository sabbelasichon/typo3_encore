<?php
declare(strict_types = 1);

namespace Ssch\Typo3Encore\ViewHelpers;

/**
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Ssch\Typo3Encore\Asset\EntrypointLookupCollectionInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

final class WebpackCssFilesViewHelper extends AbstractViewHelper
{

    /**
     * @var EntrypointLookupCollectionInterface
     */
    private $entrypointLookupCollection;

    public function __construct(EntrypointLookupCollectionInterface $entrypointLookupCollection)
    {
        $this->entrypointLookupCollection = $entrypointLookupCollection;
    }

    public function initializeArguments(): void
    {
        $this->registerArgument('entryName', 'string', 'The entry name', true);
        $this->registerArgument('buildName', 'string', 'The build name', false, '_default');
    }

    public function render(): array
    {
        $entryPointLookup = $this->entrypointLookupCollection->getEntrypointLookup($this->arguments['buildName']);
        return $entryPointLookup->getCssFiles($this->arguments['entryName']);
    }
}
