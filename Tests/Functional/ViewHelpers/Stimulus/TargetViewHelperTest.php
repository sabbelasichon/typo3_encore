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
use PHPUnit\Framework\Attributes\DataProvider;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextFactory;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use TYPO3Fluid\Fluid\View\TemplateView;

final class TargetViewHelperTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = ['typo3conf/ext/typo3_encore'];

    protected bool $initializeDatabase = false;

    #[DataProvider('provideRenderStimulusTarget')]
    public function testRenderData(mixed $controllerName, ?string $targetName, string $expected): void
    {
        /** @var RenderingContext $context */
        $context = $this->get(RenderingContextFactory::class)->create();
        $context->getViewHelperResolver()
            ->addNamespace('encore', 'Ssch\\Typo3Encore\\ViewHelpers');
        $context->getVariableProvider()
            ->add('controllerName', $controllerName);
        $context->getVariableProvider()
            ->add('targetNames', $targetName);
        $context->getTemplatePaths()
            ->setTemplateSource('{encore:stimulus.target(controllerName: controllerName, targetNames: targetNames)}');

        self::assertSame($expected, (new TemplateView($context))->render());
    }

    public static function provideRenderStimulusTarget(): Generator
    {
        yield 'simple' => [
            'controllerName' => 'my-controller',
            'targetName' => 'myTarget',
            'expected' => 'data-my-controller-target="myTarget"',
        ];

        yield 'normalize-name' => [
            'controllerName' => '@symfony/ux-dropzone/dropzone',
            'targetName' => 'myTarget',
            'expected' => 'data-symfony--ux-dropzone--dropzone-target="myTarget"',
        ];

        yield 'multiple' => [
            'controllerName' => [
                'my-controller' => 'myTarget',
                '@symfony/ux-dropzone/dropzone' => 'anotherTarget fooTarget',
            ],
            'targetName' => null,
            'expected' => 'data-my-controller-target="myTarget" data-symfony--ux-dropzone--dropzone-target="anotherTarget fooTarget"',
        ];
    }
}
