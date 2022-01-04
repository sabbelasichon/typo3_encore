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

final class ControllerViewHelper extends AbstractViewHelper
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
            'controllerValues',
            'array',
            'array of data if a string is passed to the 1st argument',
            false,
            []
        );
    }

    public function render(): string
    {
        $dataOrControllerName = $this->arguments['dataOrControllerName'];
        $controllerValues = $this->arguments['controllerValues'];

        if (\is_string($dataOrControllerName)) {
            $data = [
                $dataOrControllerName => $controllerValues,
            ];
        } else {
            if ($controllerValues) {
                throw new InvalidArgumentException(
                    'You cannot pass an array to the first and second argument of stimulus_controller(): check the documentation.'
                );
            }

            $data = $dataOrControllerName;

            if (! $data) {
                return '';
            }
        }

        $controllers = [];
        $values = [];

        foreach ($data as $controllerName => $controllerValues) {
            $controllerName = $this->normalizeControllerName($controllerName);
            $controllers[] = $controllerName;

            foreach ($controllerValues as $key => $value) {
                if (null === $value) {
                    continue;
                }

                if (! is_scalar($value)) {
                    $value = json_encode($value);
                }

                if (\is_bool($value)) {
                    $value = $value ? 'true' : 'false';
                }

                $key = $this->normalizeKeyName($key);

                $values[] = 'data-' . $controllerName . '-' . $key . '-value="' . $value . '"';
            }
        }

        return rtrim('data-controller="' . implode(' ', $controllers) . '" ' . implode(' ', $values));
    }
}
