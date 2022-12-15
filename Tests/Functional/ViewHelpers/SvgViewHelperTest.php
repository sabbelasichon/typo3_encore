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
    protected array $testExtensionsToLoad = ['typo3conf/ext/typo3_encore'];

    protected array $pathsToLinkInTestInstance = [
      'typo3conf/ext/typo3_encore/Tests/Functional/ViewHelpers/Fixtures/fileadmin/user_upload' => 'fileadmin/user_upload'
    ];

    /**
     * @var string
     */
    private const ID = '2433esds';

    protected function setUp(): void
    {
        parent::setUp();
        $this->view = GeneralUtility::makeInstance(StandaloneView::class);

    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(array $arguments, string $expected): void
    {
        $arguments['src'] = 'fileadmin/user_upload/1670925270police-station-building.svg';

        $templateSourceArguments = [];
        foreach (array_keys($arguments) as $key) {
            $templateSourceArguments[] = sprintf('%s={%s}', $key, $key);
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
                    '<svg xmlns="http://www.w3.org/2000/svg" focusable="false" role="img"><use xlink:href="#name" /></svg>'
                ),
            ],
//            [
//                [
//                    'name' => 1420,
//                ],
//                '<svg xmlns="http://www.w3.org/2000/svg" focusable="false" role="img"><use xlink:href="#1420" /></svg>',
//            ],
//            [
//                [
//                    'name' => 3333,
//                    'title' => 1222,
//                    'description' => 1420,
//                ],
//                sprintf(
//                    '<svg aria-labelledby="title-%1$s description-%1$s" xmlns="http://www.w3.org/2000/svg" focusable="false" role="img"><title id="title-%1$s">1222</title><desc id="description-%1$s">1420</desc><use xlink:href="#3333" /></svg>',
//                    self::ID
//                ),
//            ],
//            [
//                [
//                    'name' => 'name',
//                    'title' => 'Title',
//                    'description' => 'Description',
//                    'width' => 100,
//                    'height' => 100,
//                    'role' => 'foo',
//                ],
//                sprintf(
//                    '<svg aria-labelledby="title-%1$s description-%1$s" width="100" height="100" xmlns="http://www.w3.org/2000/svg" focusable="false" role="foo"><title id="title-%1$s">Title</title><desc id="description-%1$s">Description</desc><use xlink:href="#name" /></svg>',
//                    self::ID
//                ),
//            ],
//            [
//                [
//                    'name' => 'foobar',
//                    'title' => 'Title',
//                    'description' => 'Description',
//                    'width' => 100,
//                    'height' => 100,
//                    'inline' => true,
//                ],
//                sprintf(
//                    '<svg aria-labelledby="title-%1$s description-%1$s" viewBox="0 0 45 45" width="100" height="100" xmlns="http://www.w3.org/2000/svg" focusable="false" role="img"><title id="title-%1$s">Title</title><desc id="description-%1$s">Description</desc><circle cx="50" cy="50" r="40" stroke="black" stroke-width="3" fill="red"/></svg>',
//                    self::ID
//                ),
//            ],
//            [
//                [
//                    'name' => 'id-invalid',
//                    'title' => 'Title',
//                    'description' => 'Description',
//                    'width' => 100,
//                    'height' => 100,
//                    'inline' => true,
//                ],
//                sprintf(
//                    '<svg aria-labelledby="title-%1$s description-%1$s" width="100" height="100" xmlns="http://www.w3.org/2000/svg" focusable="false" role="img"><title id="title-%1$s">Title</title><desc id="description-%1$s">Description</desc></svg>',
//                    self::ID
//                ),
//            ],
        ];
    }

    private function getMockSvg(): string
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><defs><symbol viewBox="0 0 45 45" id="foobar"><circle cx="50" cy="50" r="40" stroke="black" stroke-width="3" fill="red"/></symbol></defs><use id="foobar-usage" xlink:href="#foobar" class="sprite-symbol-usage"/></svg>';
    }
}
