<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\ViewHelpers\Stimulus;

use Ssch\Typo3Encore\ViewHelpers\Stimulus\Dto\StimulusActionsDto;
use Ssch\Typo3Encore\ViewHelpers\Stimulus\Dto\StimulusControllersDto;
use Ssch\Typo3Encore\ViewHelpers\Stimulus\Dto\StimulusTargetsDto;

abstract class AbstractViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper
{
    protected $escapeOutput = false;

    /**
     * @param string|array $controllerName the Stimulus controller name
     * @param array $controllerValues array of controller values
     * @param array $controllerClasses array of controller CSS classes
     */
    public function renderStimulusController(
        $controllerName,
        array $controllerValues = [],
        array $controllerClasses = []
    ): StimulusControllersDto {
        $dto = new StimulusControllersDto();

        if (\is_array($controllerName)) {
            trigger_deprecation(
                'symfony/webpack-encore-bundle',
                'v1.15.0',
                'Passing an array as first argument of stimulus_controller() is deprecated.',
                E_USER_DEPRECATED
            );

            if ([] !== $controllerValues || [] !== $controllerClasses) {
                throw new \InvalidArgumentException(
                    'You cannot pass an array to the first and second/third argument of stimulus_controller(): check the documentation.'
                );
            }

            $data = $controllerName;

            foreach ($data as $controller => $values) {
                $dto->addController($controller, $values);
            }

            return $dto;
        }

        $dto->addController($controllerName, $controllerValues, $controllerClasses);

        return $dto;
    }

    /**
     * @param string|array $controllerName the Stimulus controller name
     * @param array $parameters Parameters to pass to the action. Optional.
     */
    public function renderStimulusAction(
        $controllerName,
        string $actionName = null,
        string $eventName = null,
        array $parameters = []
    ): StimulusActionsDto {
        $dto = new StimulusActionsDto();

        if (\is_array($controllerName)) {
            trigger_deprecation(
                'symfony/webpack-encore-bundle',
                'v1.15.0',
                'Passing an array as first argument of stimulus_action() is deprecated.',
                E_USER_DEPRECATED
            );

            if (null !== $actionName || null !== $eventName || [] !== $parameters) {
                throw new \InvalidArgumentException(
                    'You cannot pass a string to the second or third argument nor an array to the fourth argument while passing an array to the first argument of stimulus_action(): check the documentation.'
                );
            }

            $data = $controllerName;

            foreach ($data as $controller => $controllerActions) {
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

                    foreach ($controllerAction as $event => $action) {
                        $dto->addAction($controller, $action, \is_string($event) ? $event : null);
                    }
                }
            }

            return $dto;
        }

        $dto->addAction($controllerName, (string) $actionName, $eventName, $parameters);

        return $dto;
    }

    public function appendStimulusController(
        StimulusControllersDto $dto,
        string $controllerName,
        array $controllerValues = [],
        array $controllerClasses = []
    ): StimulusControllersDto {
        $dto->addController($controllerName, $controllerValues, $controllerClasses);

        return $dto;
    }

    /**
     * @param array $parameters Parameters to pass to the action. Optional.
     */
    public function appendStimulusAction(
        StimulusActionsDto $dto,
        string $controllerName,
        string $actionName,
        string $eventName = null,
        array $parameters = []
    ): StimulusActionsDto {
        $dto->addAction($controllerName, $actionName, $eventName, $parameters);

        return $dto;
    }

    /**
     * @param string|array $controllerName the Stimulus controller name
     * @param string|null $targetNames The space-separated list of target names if a string is passed to the 1st argument. Optional.
     */
    public function renderStimulusTarget($controllerName, string $targetNames = null): StimulusTargetsDto
    {
        $dto = new StimulusTargetsDto();
        if (\is_array($controllerName)) {
            trigger_deprecation(
                'symfony/webpack-encore-bundle',
                'v1.15.0',
                'Passing an array as first argument of stimulus_target() is deprecated.',
                E_USER_DEPRECATED
            );

            if (null !== $targetNames) {
                throw new \InvalidArgumentException(
                    'You cannot pass a string to the second argument while passing an array to the first argument of stimulus_target(): check the documentation.'
                );
            }

            $data = $controllerName;

            foreach ($data as $controller => $targets) {
                $dto->addTarget($controller, $targets);
            }

            return $dto;
        }

        $dto->addTarget($controllerName, $targetNames);

        return $dto;
    }

    /**
     * @param string $controllerName the Stimulus controller name
     * @param string|null $targetNames The space-separated list of target names if a string is passed to the 1st argument. Optional.
     */
    public function appendStimulusTarget(
        StimulusTargetsDto $dto,
        string $controllerName,
        string $targetNames = null
    ): StimulusTargetsDto {
        $dto->addTarget($controllerName, $targetNames);

        return $dto;
    }
}
