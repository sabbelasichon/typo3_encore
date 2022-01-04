<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Tests\Unit\ViewHelpers\Stimulus;

use Generator;
use Prophecy\PhpUnit\ProphecyTrait;
use Ssch\Typo3Encore\Tests\Unit\ViewHelpers\ViewHelperBaseTestcase;
use Ssch\Typo3Encore\ViewHelpers\Stimulus\ActionViewHelper;

final class ActionViewHelperTest extends ViewHelperBaseTestcase
{
    use ProphecyTrait;

    protected ActionViewHelper $actionViewHelper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actionViewHelper = new ActionViewHelper();
    }

    /**
     * @param mixed $dataOrControllerName
     * @dataProvider provideRenderStimulusAction
     */
    public function testRenderData(
        $dataOrControllerName,
        ?string $actionName,
        ?string $eventName,
        string $expected
    ): void {
        $this->setArgumentsUnderTest($this->actionViewHelper, [
            'dataOrControllerName' => $dataOrControllerName,
            'actionName' => $actionName,
            'eventName' => $eventName,
        ]);
        self::assertSame($expected, $this->actionViewHelper->initializeArgumentsAndRender());
    }

    public function provideRenderStimulusAction(): Generator
    {
        yield 'with default event' => [
            'dataOrControllerName' => 'my-controller',
            'actionName' => 'onClick',
            'eventName' => null,
            'expected' => 'data-action="my-controller#onClick"',
        ];

        yield 'with custom event' => [
            'dataOrControllerName' => 'my-controller',
            'actionName' => 'onClick',
            'eventName' => 'click',
            'expected' => 'data-action="click->my-controller#onClick"',
        ];

        yield 'multiple actions, with default event' => [
            'dataOrControllerName' => [
                'my-controller' => 'onClick',
                'my-second-controller' => ['onClick', 'onSomethingElse'],
                'foo/bar-controller' => 'onClick',
            ],
            'actionName' => null,
            'eventName' => null,
            'expected' => 'data-action="my-controller#onClick my-second-controller#onClick my-second-controller#onSomethingElse foo--bar-controller#onClick"',
        ];

        yield 'multiple actions, with custom event' => [
            'dataOrControllerName' => [
                'my-controller' => [
                    'click' => 'onClick',
                ],
                'my-second-controller' => [[
                    'click' => 'onClick',
                ], [
                    'change' => 'onSomethingElse',
                ]],
                'resize-controller' => [
                    'resize@window' => 'onWindowResize',
                ],
                'foo/bar-controller' => [

                    'click' => 'onClick',
                ],
            ],
            'actionName' => null,
            'eventName' =>
 null,
            'expected' => 'data-action="click->my-controller#onClick click->my-second-controller#onClick change->my-second-controller#onSomethingElse resize@window->resize-controller#onWindowResize click->foo--bar-controller#onClick"',
        ];

        yield 'multiple actions, with default and custom event' => [
            'dataOrControllerName' => [
                'my-controller' => [
                    'click' => 'onClick',
                ],
                'my-second-controller' => [
                    'onClick', [
                        'click' => 'onAnotherClick',
                    ], [
                        'change' =>
                         'onSomethingElse',
                    ], ],
                'resize-controller' => [
                    'resize@window' => 'onWindowResize',
                ],
                'foo/bar-controller' =>
 [
     'click' => 'onClick',
 ],
            ],
            'actionName' => null,
            'eventName'
 => null,
            'expected' => 'data-action="click->my-controller#onClick my-second-controller#onClick click->my-second-controller#onAnotherClick change->my-second-controller#onSomethingElse resize@window->resize-controller#onWindowResize click->foo--bar-controller#onClick"',
        ];

        yield 'normalize-name, with default event' => [
            'dataOrControllerName' => '@symfony/ux-dropzone/dropzone',
            'actionName' => 'onClick',
            'eventName' => null,
            'expected' => 'data-action="symfony--ux-dropzone--dropzone#onClick"',
        ];

        yield 'normalize-name, with custom event' => [
            'dataOrControllerName' => '@symfony/ux-dropzone/dropzone',
            'actionName' => 'onClick',
            'eventName' => 'click',
            'expected' => 'data-action="click->symfony--ux-dropzone--dropzone#onClick"',
        ];
    }
}
