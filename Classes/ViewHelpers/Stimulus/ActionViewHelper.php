<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\ViewHelpers\Stimulus;

use InvalidArgumentException;

/**
 * Copyright (c) 2004-2018 Fabien Potencier
 */
final class ActionViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument(
            'dataOrControllerName',
            'string|array',
            'This can either be a map of controller names as keys set to their "values". Or this can be a string controller name and data is passed as the 2nd argument.',
            true
        );
        $this->registerArgument(
            'eventName',
            'string',
            'The action to trigger if a string is passed to the 1st argument. Optional.',
            false
        );
        $this->registerArgument(
            'actionName',
            'string',
            'The event to listen to trigger if a string is passed to the 1st argument. Optional.',
            false
        );
    }

    public function render(): string
    {
        $dataOrControllerName = $this->arguments['dataOrControllerName'];
        $eventName = $this->arguments['eventName'];
        $actionName = $this->arguments['actionName'];

        if (\is_string($dataOrControllerName)) {
            $data = [
                $dataOrControllerName => null === $eventName ? [[$actionName]] : [[
                    $eventName => $actionName,
                ]],
            ];
        } else {
            if ($actionName || $eventName) {
                throw new InvalidArgumentException(
                    'You cannot pass a string to the second or third argument while passing an array to the first argument of stimulus_action(): check the documentation.'
                );
            }

            $data = $dataOrControllerName;

            if (! $data) {
                return '';
            }
        }

        $actions = [];

        foreach ($data as $controllerName => $controllerActions) {
            $controllerName = $this->normalizeControllerName($controllerName);

            if (\is_string($controllerActions)) {
                $controllerActions = [[$controllerActions]];
            }

            foreach ($controllerActions as $possibleEventName => $controllerAction) {
                if (\is_string($possibleEventName) && \is_string($controllerAction)) {
                    $controllerAction = [
                        $possibleEventName => $controllerAction,
                    ];
                } elseif (\is_string($controllerAction)) {
                    $controllerAction = [$controllerAction];
                }

                foreach ($controllerAction as $eventName => $actionName) {
                    $action = $controllerName . '#' . $actionName;

                    if (\is_string($eventName)) {
                        $action = $eventName . '->' . $action;
                    }

                    $actions[] = $action;
                }
            }
        }

        return 'data-action="' . implode(' ', $actions) . '"';
    }
}
