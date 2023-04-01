<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\ViewHelpers\Stimulus;

/**
 * Copyright (c) 2004-2018 Fabien Potencier
 */
final class TargetViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument('controllerName', 'string|array', 'The Stimulus controller name to render.', true);
        $this->registerArgument(
            'targetNames',
            'string',
            'The space-separated list of target names if a string is passed to the 1st argument. Optional.',
        );
    }

    public function render(): string
    {
        return $this->renderStimulusTarget(
            $this->arguments['controllerName'],
            $this->arguments['targetNames']
        )->__toString();
    }
}
