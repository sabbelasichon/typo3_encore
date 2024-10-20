<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Tests\Functional;

use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class IncludeFilesTest extends FunctionalTestCase
{
    /**
     * @var int
     */
    private const ROOT_PAGE_UID = 1;

    protected array $testExtensionsToLoad = ['typo3conf/ext/typo3_encore'];

    protected array $pathsToLinkInTestInstance = [
        'typo3conf/ext/typo3_encore/Tests/Functional/Fixtures/sites' => 'typo3conf/sites',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->importCSVDataSet(__DIR__ . '/Fixtures/pages.csv');
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function testAddFiles(): void
    {
        $this->setUpFrontendRootPage(
            self::ROOT_PAGE_UID,
            ['EXT:typo3_encore/Tests/Functional/Fixtures/Frontend/MainRenderer.typoscript']
        );
        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(self::ROOT_PAGE_UID));

        $content = $response->getBody()
            ->__toString();
        self::assertStringContainsString('TYPO3 Webpack Encore - Modern Frontend Development', $content);
        self::assertStringContainsString('main.css', $content);
        self::assertStringContainsString('main.js', $content);
        self::assertStringContainsString(
            'sha384-ysKW+jP4sNH9UfX9+fqN4iC/RB3L9jmWUd8ABJrBbAHFwL6wNmvNT5x178Fx6Xh0',
            $content
        );
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function testAddFilesWithAbsRefPrefix(): void
    {
        $this->setUpFrontendRootPage(
            self::ROOT_PAGE_UID,
            ['EXT:typo3_encore/Tests/Functional/Fixtures/Frontend/MainRendererAbsRefPrefix.typoscript']
        );
        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(self::ROOT_PAGE_UID));

        $content = $response->getBody()
            ->__toString();
        self::assertStringContainsString(
            'https://www.domain.com/foo/typo3conf/ext/typo3_encore/Tests/Functional/Fixtures/Frontend/Resources/Public/main.css',
            $content
        );
        self::assertStringContainsString(
            'https://www.domain.com/foo/typo3conf/ext/typo3_encore/Tests/Functional/Fixtures/Frontend/Resources/Public/main.js',
            $content
        );
        self::assertStringContainsString(
            'sha384-ysKW+jP4sNH9UfX9+fqN4iC/RB3L9jmWUd8ABJrBbAHFwL6wNmvNT5x178Fx6Xh0',
            $content
        );
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function testAddFilesWithHtml5DocType(): void
    {
        $this->setUpFrontendRootPage(
            self::ROOT_PAGE_UID,
            ['EXT:typo3_encore/Tests/Functional/Fixtures/Frontend/MainRendererHtml5.typoscript']
        );
        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(self::ROOT_PAGE_UID));

        $content = $response->getBody()
            ->__toString();
        self::assertStringNotContainsString('text/javascript', $content);
    }
}
