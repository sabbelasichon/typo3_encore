<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Tests\Unit\Form\FormDataProvider;

use Iterator;
use Ssch\Typo3Encore\Asset\EntrypointLookupCollectionInterface;
use Ssch\Typo3Encore\Asset\EntrypointLookupInterface;
use Ssch\Typo3Encore\Asset\IntegrityDataProviderInterface;
use Ssch\Typo3Encore\Form\FormDataProvider\RichtextEncoreConfiguration;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class RichtextEncoreConfigurationTest extends UnitTestCase
{
    protected RichtextEncoreConfiguration $subject;

    protected function setUp(): void
    {
        $this->subject = new RichtextEncoreConfiguration($this->createEntrypointLookUpCollection());
    }

    /**
     * @dataProvider provideRteConfigurationWithEncoreFiles
     *
     * @param string|string[] $contentsCss
     */
    public function testFormConfiguration($contentsCss, array $expected): void
    {
        $data = $this->createFormData($contentsCss);

        $data = $this->subject->addData($data);
        $valueAfterProcessing = $data['processedTca']['columns']['testColumn']['config']['richtextConfiguration']['editor']['config']['contentsCss'];
        self::assertEquals($expected, $valueAfterProcessing);
    }

    public function provideRteConfigurationWithoutEncoreFiles(): Iterator
    {
        yield [
            'contentCss' => 'EXT:rte_ckeditor/Resources/Public/Css/contents.css',
            'expected' => ['EXT:rte_ckeditor/Resources/Public/Css/contents.css'],
        ];
        yield [
            'contentCss' => ['EXT:rte_ckeditor/Resources/Public/Css/contents.css'],
            'expected' => ['EXT:rte_ckeditor/Resources/Public/Css/contents.css'],
        ];
    }

    public function provideRteConfigurationWithEncoreFiles(): Iterator
    {
        yield [
            'contentCss' => 'typo3_encore:entryPoint',
            'expected' => ['file.css'],
        ];

        yield [
            'contentCss' => 'typo3_encore:_default:entryPoint',
            'expected' => ['file.css'],
        ];

        yield [
            'contentCss' => ['typo3_encore:entryPoint'],
            'expected' => ['file.css'],
        ];

        yield [
            'contentCss' => ['typo3_encore:_default:entryPoint'],
            'expected' => ['file.css'],
        ];

        yield [
            'contentCss' => ['typo3_encore:entryPoint', 'EXT:rte_ckeditor/Resources/Public/Css/contents.css'],
            'expected' => ['file.css', 'EXT:rte_ckeditor/Resources/Public/Css/contents.css'],
        ];

        yield [
            'contentCss' => ['EXT:rte_ckeditor/Resources/Public/Css/contents.css', 'typo3_encore:entryPoint'],
            'expected' => ['EXT:rte_ckeditor/Resources/Public/Css/contents.css', 'file.css'],
        ];
    }

    private function createEntrypointLookUpCollection(): EntrypointLookupCollectionInterface
    {
        return new class() implements EntrypointLookupCollectionInterface {
            public function getEntrypointLookup(string $buildName = null): EntrypointLookupInterface
            {
                if ('_default' !== $buildName) {
                    throw new \Exception('Invalid buildName in test case', 1645708934);
                }
                return new class() implements EntrypointLookupInterface, IntegrityDataProviderInterface {
                    public function getJavaScriptFiles(string $entryName): array
                    {
                        return 'entryPoint' === $entryName ? ['file.js'] : [];
                    }

                    public function getCssFiles(string $entryName): array
                    {
                        return 'entryPoint' === $entryName ? ['file.css'] : [];
                    }

                    public function reset(): void
                    {
                    }

                    public function getIntegrityData(): array
                    {
                        return [
                            'file.js' => 'foobarbaz',
                        ];
                    }
                };
            }
        };
    }

    /**
     * @param string|string[] $contentsCss
     */
    private function createFormData($contentsCss): array
    {
        return [
            'processedTca' => [
                'columns' => [
                    'testColumn' => [
                        'config' => [
                            'type' => 'text',
                            'enableRichtext' => true,
                            'richtextConfiguration' => [
                                'editor' => [
                                    'config' => [
                                        'contentsCss' => $contentsCss,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
