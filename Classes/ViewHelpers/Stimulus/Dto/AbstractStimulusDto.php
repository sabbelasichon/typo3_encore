<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\ViewHelpers\Stimulus\Dto;

abstract class AbstractStimulusDto
{
    abstract public function toArray(): array;

    protected function getFormattedControllerName(string $controllerName): string
    {
        return $this->escapeAsHtmlAttr($this->normalizeControllerName($controllerName));
    }

    /**
     * @param mixed $value
     */
    protected function getFormattedValue($value): string
    {
        if ((\is_object($value) && \is_callable([$value, '__toString']))) {
            $value = (string) $value;
        } elseif (! \is_scalar($value)) {
            $value = json_encode($value);
        } elseif (\is_bool($value)) {
            $value = $value ? 'true' : 'false';
        }

        return (string) $value;
    }

    protected function escapeAsHtmlAttr(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES);
    }

    /**
     * Normalize a Stimulus controller name into its HTML equivalent (no special character and / becomes --).
     *
     * @see https://stimulus.hotwired.dev/reference/controllers
     */
    private function normalizeControllerName(string $controllerName): string
    {
        return (string) preg_replace('/^@/', '', str_replace('_', '-', str_replace('/', '--', $controllerName)));
    }
}
