<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Tests\Functional\ViewHelpers\Stimulus;

use Generator;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class ControllerViewHelperTest extends FunctionalTestCase
{
    protected StandaloneView $view;

    protected function setUp(): void
    {
        $this->testExtensionsToLoad[] = 'typo3conf/ext/typo3_encore';
        $this->initializeDatabase = false;
        parent::setUp();
        $this->view = GeneralUtility::makeInstance(StandaloneView::class);
    }

    /**
     * @param mixed $controllerName
     * @dataProvider provideRenderStimulusController
     */
    public function testRenderData($controllerName, array $controllerValues, string $expected): void
    {
        $this->view->assignMultiple([
            'controllerName' => $controllerName,
            'controllerValues' => $controllerValues,
        ]);
        $this->view->getRenderingContext()
            ->getViewHelperResolver()
            ->addNamespace('encore', 'Ssch\\Typo3Encore\\ViewHelpers');
        $this->view->setTemplateSource(
            '{encore:stimulus.controller(controllerName: controllerName, controllerValues: controllerValues)}'
        );
        self::assertSame($expected, $this->view->render());
    }

    public function provideRenderStimulusController(): Generator
    {
        yield 'empty' => [
            'controllerName' => [],
            'controllerValues' => [],
            'expected' => '',
        ];

        yield 'single-controller-no-data' => [
            'controllerName' => [
                'my-controller' => [],
            ],
            'controllerValues' => [],
            'expected' => 'data-controller="my-controller"',
        ];

        yield 'single-controller-scalar-data' => [
            'controllerName' => [
                'my-controller' => [
                    'myValue' => 'scalar-value',
                ],
            ],
            'controllerValues' => [],
            'expected' => 'data-controller="my-controller" data-my-controller-my-value-value="scalar-value"',
        ];

        yield 'single-controller-typed-data' => [
            'controllerName' => [
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
            'controllerName' => [
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
            'controllerName' => [
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
            'controllerName' => [
                '@symfony/ux-dropzone/dropzone' => [
                    'my"Key"' => true,
                ],
            ],
            'controllerValues' => [],
            'expected' => 'data-controller="symfony--ux-dropzone--dropzone" data-symfony--ux-dropzone--dropzone-my-key-value="true"',
        ];

        yield 'short-single-controller-no-data' => [
            'controllerName' => 'my-controller',
            'controllerValues' => [],
            'expected' => 'data-controller="my-controller"',
        ];

        yield 'short-single-controller-with-data' => [
            'controllerName' => 'my-controller',
            'controllerValues' => [
                'myValue' => 'scalar-value',
            ],
            'expected' => 'data-controller="my-controller" data-my-controller-my-value-value="scalar-value"',
        ];

        yield 'false-attribute-value-renders-false' => [
            'controllerName' => 'false-controller',
            'controllerValues' => [
                'isEnabled' => false,
            ],
            'expected' => 'data-controller="false-controller" data-false-controller-is-enabled-value="false"',
        ];

        yield 'true-attribute-value-renders-true' => [
            'controllerName' => 'true-controller',
            'controllerValues' => [
                'isEnabled' => true,
            ],
            'expected' => 'data-controller="true-controller" data-true-controller-is-enabled-value="true"',
        ];

        yield 'null-attribute-value-does-not-render' => [
            'controllerName' => 'null-controller',
            'controllerValues' => [
                'firstName' => null,
            ],
            'expected' => 'data-controller="null-controller"',
        ];
    }
}
