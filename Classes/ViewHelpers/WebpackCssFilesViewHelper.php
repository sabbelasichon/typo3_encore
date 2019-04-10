<?php
declare(strict_types=1);


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

use Ssch\Typo3Encore\Asset\EntrypointLookupInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

final class WebpackCssFilesViewHelper extends AbstractViewHelper
{

    /**
     * @var EntrypointLookupInterface
     */
    private $entrypointLookup;

    /**
     * WebpackCssFiles constructor.
     *
     * @param EntrypointLookupInterface $entrypointLookup
     */
    public function __construct(EntrypointLookupInterface $entrypointLookup)
    {
        $this->entrypointLookup = $entrypointLookup;
    }


    public function initializeArguments(): void
    {
        $this->registerArgument('entryName', 'string', 'The entry name', true);
    }

    public function render(): array
    {
        return $this->entrypointLookup->getCssFiles($this->arguments['entryName']);
    }

}