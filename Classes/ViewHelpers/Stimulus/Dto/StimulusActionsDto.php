<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\ViewHelpers\Stimulus\Dto;

final class StimulusActionsDto extends AbstractStimulusDto implements \Stringable
{
    private array $actions = [];

    private array $parameters = [];

    public function __toString(): string
    {
        if (\count($this->actions) === 0) {
            return '';
        }

        return rtrim(
            'data-action="' . implode(' ', $this->actions) . '" ' . implode(' ', array_map(
                fn (string $attribute, string $value): string => $attribute . '="' . $this->escapeAsHtmlAttr(
                    $value
                ) . '"',
                array_keys($this->parameters),
                $this->parameters
            ))
        );
    }

    /**
     * @param array $parameters Parameters to pass to the action. Optional.
     */
    public function addAction(
        string $controllerName,
        string $actionName,
        ?string $eventName = null,
        array $parameters = []
    ): void {
        $controllerName = $this->getFormattedControllerName($controllerName);
        $action = $controllerName . '#' . $this->escapeAsHtmlAttr($actionName);

        if ($eventName !== null) {
            $action = $eventName . '->' . $action;
        }

        $this->actions[] = $action;

        foreach ($parameters as $name => $value) {
            $this->parameters['data-' . $controllerName . '-' . $name . '-param'] = $this->getFormattedValue($value);
        }
    }

    public function toArray(): array
    {
        return [
            'data-action' => implode(' ', $this->actions),
        ] + $this->parameters;
    }
}
