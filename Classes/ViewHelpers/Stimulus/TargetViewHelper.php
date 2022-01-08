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
final class TargetViewHelper extends AbstractViewHelper
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
            'targetNames',
            'string',
            'The space-separated list of target names if a string is passed to the 1st argument. Optional.',
            false
        );
    }

    public function render(): string
    {
        $dataOrControllerName = $this->arguments['dataOrControllerName'];
        $targetNames = $this->arguments['targetNames'];
        if (\is_string($dataOrControllerName)) {
            $data = [
                $dataOrControllerName => $targetNames,
            ];
        } else {
            if ($targetNames) {
                throw new InvalidArgumentException(
                    'You cannot pass a string to the second argument while passing an array to the first argument of stimulus_target(): check the documentation.'
                );
            }

            $data = $dataOrControllerName;

            if (! $data) {
                return '';
            }
        }

        $targets = [];

        foreach ($data as $controllerName => $targetName) {
            $controllerName = $this->normalizeControllerName($controllerName);

            $targets['data-' . $controllerName . '-target'] = $targetName;
        }

        return implode(' ', array_map(static function (string $attribute, string $value): string {
            return $attribute . '="' . $value . '"';
        }, array_keys($targets), $targets));
    }
}
