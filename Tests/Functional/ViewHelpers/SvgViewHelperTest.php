<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Tests\Functional\ViewHelpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class SvgViewHelperTest extends FunctionalTestCase
{
    /**
     * @var string
     */
    private const ID = 'fixed';

    protected StandaloneView $view;

    protected function setUp(): void
    {
        $this->pathsToLinkInTestInstance['typo3conf/ext/typo3_encore/Tests/Functional/ViewHelpers/Fixtures/fileadmin/user_upload'] = 'fileadmin/user_upload';
        $this->testExtensionsToLoad[] = 'typo3conf/ext/typo3_encore';
        $this->initializeDatabase = false;
        parent::setUp();
        $this->view = GeneralUtility::makeInstance(StandaloneView::class);
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(array $arguments, string $expected): void
    {
        $arguments['src'] = 'fileadmin/user_upload/sprite.svg';

        $templateSourceArguments = [];
        foreach (array_keys($arguments) as $key) {
            $templateSourceArguments[] = sprintf('%s="{%s}"', $key, $key);
        }

        $templateSource = sprintf('<encore:svg %s />', implode(' ', $templateSourceArguments));

        $this->view->assignMultiple($arguments);
        $this->view->getRenderingContext()
            ->getViewHelperResolver()
            ->addNamespace('encore', 'Ssch\\Typo3Encore\\ViewHelpers');
        $this->view->setTemplateSource($templateSource);
        self::assertSame($expected, $this->view->render());
    }

    public function renderDataProvider(): array
    {
        return [
            [
                [
                    'name' => 'name',
                ],
                sprintf(
                    '<svg xmlns="http://www.w3.org/2000/svg" focusable="false" role="img"><use xlink:href="fileadmin/user_upload/sprite.svg#name" /></svg>'
                ),
            ],
            [
                [
                    'name' => 1420,
                ],
                '<svg xmlns="http://www.w3.org/2000/svg" focusable="false" role="img"><use xlink:href="fileadmin/user_upload/sprite.svg#1420" /></svg>',
            ],
            [
                [
                    'name' => 3333,
                    'title' => 1222,
                    'description' => 1420,
                ],
                sprintf(
                    '<svg aria-labelledby="title-%1$s description-%1$s" xmlns="http://www.w3.org/2000/svg" focusable="false" role="img"><title id="title-%1$s">1222</title><desc id="description-%1$s">1420</desc><use xlink:href="fileadmin/user_upload/sprite.svg#3333" /></svg>',
                    self::ID
                ),
            ],
            [
                [
                    'name' => 'name',
                    'title' => 'Title',
                    'description' => 'Description',
                    'width' => 100,
                    'height' => 100,
                    'role' => 'foo',
                ],
                sprintf(
                    '<svg aria-labelledby="title-%1$s description-%1$s" width="100" height="100" xmlns="http://www.w3.org/2000/svg" focusable="false" role="foo"><title id="title-%1$s">Title</title><desc id="description-%1$s">Description</desc><use xlink:href="fileadmin/user_upload/sprite.svg#name" /></svg>',
                    self::ID
                ),
            ],
            [
                [
                    'name' => 'icon-magnifier',
                    'title' => 'Title',
                    'description' => 'Description',
                    'width' => 100,
                    'height' => 100,
                    'inline' => true,
                ],
                sprintf(
                    '<svg aria-labelledby="title-%1$s description-%1$s" viewBox="0 0 24 24" width="100" height="100" xmlns="http://www.w3.org/2000/svg" focusable="false" role="img"><title id="title-%1$s">Title</title><desc id="description-%1$s">Description</desc><title>icon-magnifier</title><g fill="none" stroke="#0d59ab"><circle cx="10" cy="10" r="9"/><path d="M16 16l7 7" stroke-linecap="butt"/></g></svg>',
                    self::ID
                ),
            ],
            [
                [
                    'name' => 'id-invalid',
                    'title' => 'Title',
                    'description' => 'Description',
                    'width' => 100,
                    'height' => 100,
                    'inline' => true,
                ],
                sprintf(
                    '<svg aria-labelledby="title-%1$s description-%1$s" width="100" height="100" xmlns="http://www.w3.org/2000/svg" focusable="false" role="img"><title id="title-%1$s">Title</title><desc id="description-%1$s">Description</desc></svg>',
                    self::ID
                ),
            ],
        ];
    }
}
