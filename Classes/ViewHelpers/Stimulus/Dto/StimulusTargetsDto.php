<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\ViewHelpers\Stimulus\Dto;

final class StimulusTargetsDto extends AbstractStimulusDto
{
    private array $targets = [];

    public function __toString(): string
    {
        if (0 === \count($this->targets)) {
            return '';
        }

        return implode(' ', array_map(function (string $attribute, string $value): string {
            return $attribute . '="' . $this->escapeAsHtmlAttr($value) . '"';
        }, array_keys($this->targets), $this->targets));
    }

    /**
     * @param string      $controllerName the Stimulus controller name
     * @param string|null $targetNames    The space-separated list of target names if a string is passed to the 1st argument. Optional.
     */
    public function addTarget(string $controllerName, string $targetNames = null): void
    {
        $controllerName = $this->getFormattedControllerName($controllerName);

        $this->targets['data-' . $controllerName . '-target'] = $targetNames;
    }

    public function toArray(): array
    {
        return $this->targets;
    }
}
