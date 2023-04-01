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
final class ActionViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument('controllerName', 'string|array', 'The Stimulus controller name to render.', true);
        $this->registerArgument(
            'eventName',
            'string',
            'The event to listen to trigger if a string is passed to the 1st argument. Optional.',
        );
        $this->registerArgument(
            'actionName',
            'string',
            'The action to trigger if a string is passed to the 1st argument. Optional.'
        );
        $this->registerArgument('parameters', 'array', 'Parameters to pass to the action. Optional.', false, []);
    }

    public function render(): string
    {
        return $this->renderStimulusAction(
            $this->arguments['controllerName'],
            $this->arguments['actionName'],
            $this->arguments['eventName'],
            $this->arguments['parameters']
        )->__toString();
    }
}
