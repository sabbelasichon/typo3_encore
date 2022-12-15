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

final class TargetViewHelperTest extends FunctionalTestCase
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
     * @param mixed $dataOrControllerName
     * @dataProvider provideRenderStimulusTarget
     */
    public function testRenderData($dataOrControllerName, ?string $targetName, string $expected): void
    {
        $this->view->assignMultiple([
            'dataOrControllerName' => $dataOrControllerName,
            'targetNames' => $targetName,
        ]);
        $this->view->getRenderingContext()
            ->getViewHelperResolver()
            ->addNamespace('encore', 'Ssch\\Typo3Encore\\ViewHelpers');
        $this->view->setTemplateSource(
            '{encore:stimulus.target(dataOrControllerName: dataOrControllerName, targetNames: targetNames)}'
        );
        self::assertSame($expected, $this->view->render());
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
