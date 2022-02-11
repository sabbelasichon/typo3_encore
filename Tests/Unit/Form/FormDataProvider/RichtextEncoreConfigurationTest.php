<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Tests\Unit\Form\FormDataProvider;

use Prophecy\Prophecy\ObjectProphecy;
use Prophecy\PhpUnit\ProphecyTrait;
use Ssch\Typo3Encore\Asset\EntrypointLookupInterface;
use Ssch\Typo3Encore\Asset\EntrypointLookupCollectionInterface;
use Ssch\Typo3Encore\Asset\IntegrityDataProviderInterface;
use Ssch\Typo3Encore\Form\FormDataProvider\RichtextEncoreConfiguration;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class RichtextEncoreConfigurationTest extends UnitTestCase
{
    use ProphecyTrait;

    protected RichtextEncoreConfiguration $subject;

    /**
     * @var ObjectProphecy|EntrypointLookupCollectionInterface
     */
    protected ObjectProphecy $entryLookupCollection;

    protected function setUp(): void
    {
        $this->entryLookupCollection = $this->prophesize(EntrypointLookupCollectionInterface::class);
        $this->subject = new RichtextEncoreConfiguration($this->entryLookupCollection->reveal());
    }

    /**
     * @dataProvider rteConfiguration
     * @param string|string[] $contentsCss
     */
    public function testRichtextConfiguration($contentsCss, array $expected, bool $containsEntrypoint): void
    {
        if ($containsEntrypoint) {
            $this->entryLookupCollection->getEntrypointLookup(
                EntrypointLookupInterface::DEFAULT_BUILD
            )->shouldBeCalledOnce()
                ->willReturn($this->createEntrypointLookUpClass());
        }

        $data = [
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

        $data = $this->subject->addData($data);
        $valueAfterProcessing = $data['processedTca']['columns']['testColumn']['config']['richtextConfiguration']['editor']['config']['contentsCss'];
        self::assertEquals($expected, $valueAfterProcessing);
    }

    public function rteConfiguration(): array
    {
        return [
            ['typo3_encore:entryPoint', ['file.css'], true],
            ['typo3_encore:_default:entryPoint', ['file.css'], true],
            [['typo3_encore:entryPoint'], ['file.css'], true],
            [['typo3_encore:_default:entryPoint'], ['file.css'], true],

            ['EXT:rte_ckeditor/Resources/Public/Css/contents.css', ['EXT:rte_ckeditor/Resources/Public/Css/contents.css'], false],
            [['EXT:rte_ckeditor/Resources/Public/Css/contents.css'], ['EXT:rte_ckeditor/Resources/Public/Css/contents.css'], false],

            [['typo3_encore:entryPoint', 'EXT:rte_ckeditor/Resources/Public/Css/contents.css'], ['file.css', 'EXT:rte_ckeditor/Resources/Public/Css/contents.css'], true],
            [['EXT:rte_ckeditor/Resources/Public/Css/contents.css', 'typo3_encore:entryPoint'], ['EXT:rte_ckeditor/Resources/Public/Css/contents.css', 'file.css'], true],
        ];
    }

    private function createEntrypointLookUpClass(): EntrypointLookupInterface
    {
        return new class() implements EntrypointLookupInterface, IntegrityDataProviderInterface {
            public function getJavaScriptFiles(string $entryName): array
            {
                return ['file.js'];
            }

            public function getCssFiles(string $entryName): array
            {
                return ['file.css'];
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

    private function createEntrypointLookUpClassWithMultipleEntries(): EntrypointLookupInterface
    {
        return new class() implements EntrypointLookupInterface, IntegrityDataProviderInterface {
            public function getJavaScriptFiles(string $entryName): array
            {
                return ['file1.js', 'file2.js'];
            }

            public function getCssFiles(string $entryName): array
            {
                return ['file1.css', 'file2.css'];
            }

            public function reset(): void
            {
            }

            public function getIntegrityData(): array
            {
                return [
                    'file1.js' => 'foobarbaz1',
                    'file2.js' => 'foobarbaz2',
                ];
            }
        };
    }
}
