<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\ViewHelpers\Stimulus;

final class ControllerViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument('controllerName', 'string|array', 'The Stimulus controller name to render.', true);
        $this->registerArgument(
            'controllerValues',
            'array',
            'array of data if a string is passed to the 1st argument',
            false,
            []
        );
        $this->registerArgument(
            'controllerClasses',
            'array',
            'Array of classes to add to the controller',
            false,
            []
        );
    }

    public function render(): string
    {
        return $this->renderStimulusController(
            $this->arguments['controllerName'],
            $this->arguments['controllerValues'],
            $this->arguments['controllerClasses']
        )->__toString();
    }
}
