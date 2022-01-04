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
use Ssch\Typo3Encore\ViewHelpers\Stimulus\ControllerViewHelper;

final class ControllerViewHelperTest extends ViewHelperBaseTestcase
{
    use ProphecyTrait;

    protected ControllerViewHelper $controllerViewHelper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controllerViewHelper = new ControllerViewHelper();
    }

    /**
     * @param mixed $dataOrControllerName
     * @dataProvider provideRenderStimulusController
     */
    public function testRenderData($dataOrControllerName, array $controllerValues, string $expected): void
    {
        $this->setArgumentsUnderTest($this->controllerViewHelper, [
            'dataOrControllerName' => $dataOrControllerName,
            'controllerValues' => $controllerValues,
        ]);
        self::assertSame($expected, $this->controllerViewHelper->initializeArgumentsAndRender());
    }

    public function provideRenderStimulusController(): Generator
    {
        yield 'empty' => [
            'dataOrControllerName' => [],
            'controllerValues' => [],
            'expected' => '',
        ];

        yield 'single-controller-no-data' => [
            'dataOrControllerName' => [
                'my-controller' => [],
            ],
            'controllerValues' => [],
            'expected' => 'data-controller="my-controller"',
        ];

        yield 'single-controller-scalar-data' => [
            'dataOrControllerName' => [
                'my-controller' => [
                    'myValue' => 'scalar-value',
                ],
            ],
            'controllerValues' => [],
            'expected' => 'data-controller="my-controller" data-my-controller-my-value-value="scalar-value"',
        ];

        yield 'single-controller-typed-data' => [
            'dataOrControllerName' => [
                'my-controller' => [
                    'boolean' => true,
                    'number' => 4,
                    'string' => 'str',
                ],
            ],
            'controllerValues' => [],
            'expected' => 'data-controller="my-controller" data-my-controller-boolean-value="true" data-my-controller-number-value="4" data-my-controller-string-value="str"',
        ];

        yield 'single-controller-nested-data' => [
            'dataOrControllerName' => [
                'my-controller' => [
                    'myValue' => [
                        'nested' => 'array',
                    ],
                ],
            ],
            'controllerValues' => [],
            'expected' =>
'data-controller="my-controller" data-my-controller-my-value-value="{"nested":"array"}"',
        ];

        yield 'multiple-controllers-scalar-data' => [
            'dataOrControllerName' => [
                'my-controller' => [
                    'myValue' => 'scalar-value',
                ],
                'another-controller' => [
                    'anotherValue' => 'scalar-value 2',
                ],
            ],
            'controllerValues' => [],
            'expected' => 'data-controller="my-controller another-controller" data-my-controller-my-value-value="scalar-value" data-another-controller-another-value-value="scalar-value 2"',
        ];

        yield 'normalize-names' => [
            'dataOrControllerName' => [
                '@symfony/ux-dropzone/dropzone' => [
                    'my"Key"' => true,
                ],
            ],
            'controllerValues' => [],
            'expected' => 'data-controller="symfony--ux-dropzone--dropzone" data-symfony--ux-dropzone--dropzone-my-key-value="true"',
        ];

        yield 'short-single-controller-no-data' => [
            'dataOrControllerName' => 'my-controller',
            'controllerValues' => [],
            'expected' => 'data-controller="my-controller"',
        ];

        yield 'short-single-controller-with-data' => [
            'dataOrControllerName' => 'my-controller',
            'controllerValues' => [
                'myValue' => 'scalar-value',
            ],
            'expected' => 'data-controller="my-controller" data-my-controller-my-value-value="scalar-value"',
        ];

        yield 'false-attribute-value-renders-false' => [
            'dataOrControllerName' => 'false-controller',
            'controllerValues' => [
                'isEnabled' => false,
            ],
            'expected' => 'data-controller="false-controller" data-false-controller-is-enabled-value="false"',
        ];

        yield 'true-attribute-value-renders-true' => [
            'dataOrControllerName' => 'true-controller',
            'controllerValues' => [
                'isEnabled' => true,
            ],
            'expected' => 'data-controller="true-controller" data-true-controller-is-enabled-value="true"',
        ];

        yield 'null-attribute-value-does-not-render' => [
            'dataOrControllerName' => 'null-controller',
            'controllerValues' => [
                'firstName' => null,
            ],
            'expected' => 'data-controller="null-controller"',
        ];
    }
}
