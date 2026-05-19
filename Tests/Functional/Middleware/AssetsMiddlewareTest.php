<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Tests\Functional\Middleware;

use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use TYPO3\CMS\Core\Cache\Backend\Typo3DatabaseBackend;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class AssetsMiddlewareTest extends FunctionalTestCase
{
    /**
     * @var int
     */
    private const ROOT_PAGE_UID = 1;

    protected array $testExtensionsToLoad = ['typo3conf/ext/typo3_encore'];

    protected array $pathsToLinkInTestInstance = [
        'typo3conf/ext/typo3_encore/Tests/Functional/Fixtures/sites' => 'typo3conf/sites',
    ];

    protected array $pathsToProvideInTestInstance = [
        'typo3conf/ext/typo3_encore/Tests/Functional/Fixtures/Frontend/Resources/Public' => '_assets/build',
    ];

    // Activate page cache to test multiple requests
    protected array $configurationToUseInTestInstance = [
        'SYS' => [
            'caching' => [
                'cacheConfigurations' => [
                    'hash' => [
                        'backend' => Typo3DatabaseBackend::class,
                    ],
                    'imagesizes' => [
                        'backend' => Typo3DatabaseBackend::class,
                    ],
                    'pages' => [
                        'backend' => Typo3DatabaseBackend::class,
                    ],
                    'rootline' => [
                        'backend' => Typo3DatabaseBackend::class,
                    ],
                ],
            ],
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/pages.csv');
    }

    #[RunInSeparateProcess]
    public function testLinkHeader(): void
    {
        $this->setUpFrontendRootPage(
            self::ROOT_PAGE_UID,
            ['EXT:typo3_encore/Tests/Functional/Fixtures/Frontend/MainRenderer.typoscript']
        );

        // Subsequent requests should use the cached data
        foreach (range(1, 4) as $run) {
            $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(self::ROOT_PAGE_UID));
            $content = $response->getHeaderLine('Link');
            var_dump($response->getHeaderLine('Link'));
            self::assertMatchesRegularExpression(
                '#</_assets/build/main\.css\?\d+>; rel="preload"; as="style"#',
                $content,
                'Run ' . $run
            );
            self::assertMatchesRegularExpression(
                '#</_assets/build/main\.js\?\d+>; rel="preload"; as="script"#',
                $content,
                'Run ' . $run
            );
        }
    }
}
