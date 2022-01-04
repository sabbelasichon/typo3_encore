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
use Ssch\Typo3Encore\ViewHelpers\Stimulus\TargetViewHelper;

final class TargetViewHelperTest extends ViewHelperBaseTestcase
{
    use ProphecyTrait;

    protected TargetViewHelper $targetViewHelper;

    public function setUp(): void
    {
        parent::setUp();
        $this->targetViewHelper = new TargetViewHelper();
    }

    /**
     * @test
     * @param mixed $dataOrControllerName
     * @dataProvider provideRenderStimulusTarget
     */
    public function renderData($dataOrControllerName, ?string $targetName, string $expected): void
    {
        $this->setArgumentsUnderTest($this->targetViewHelper, ['dataOrControllerName' => $dataOrControllerName, 'targetNames' => $targetName]);
        self::assertSame($expected, $this->targetViewHelper->initializeArgumentsAndRender());
    }

    public function provideRenderStimulusTarget(): Generator
    {
        yield 'simple' => [
            'dataOrControllerName' => 'my-controller',
            'targetName' => 'myTarget',
            'expected' => 'data-my-controller-target="myTarget"',
        ];

        yield 'normalize-name' => [
            'dataOrControllerName' => '@symfony/ux-dropzone/dropzone',
            'targetName' => 'myTarget',
            'expected' => 'data-symfony--ux-dropzone--dropzone-target="myTarget"',
        ];

        yield 'multiple' => [
            'dataOrControllerName' => [
                'my-controller' => 'myTarget',
                '@symfony/ux-dropzone/dropzone' => 'anotherTarget fooTarget',
            ],
            'targetName' => null,
            'expected' => 'data-my-controller-target="myTarget" data-symfony--ux-dropzone--dropzone-target="anotherTarget fooTarget"',
        ];
    }
}
