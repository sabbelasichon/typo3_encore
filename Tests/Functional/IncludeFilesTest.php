<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Tests\Functional;

use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Exception;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class IncludeFilesTest extends FunctionalTestCase
{
    /**
     * @var int
     */
    private const ROOT_PAGE_UID = 1;

    protected function setUp(): void
    {
        $this->testExtensionsToLoad[] = 'typo3conf/ext/typo3_encore';

        parent::setUp();
        try {
            $this->importDataSet(__DIR__ . '/Fixtures/pages.xml');
        } catch (Exception $e) {
        }
    }

    public function testAddFiles(): void
    {
        $this->setUpFrontendRootPage(
            self::ROOT_PAGE_UID,
            ['EXT:typo3_encore/Tests/Functional/Fixtures/Frontend/MainRenderer.typoscript']
        );
        $this->setUpSites(self::ROOT_PAGE_UID, []);
        $response = $this->executeFrontendRequest((new InternalRequest())->withPageId(self::ROOT_PAGE_UID));

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

    public function testAddFilesWithAbsRefPrefix(): void
    {
        $this->setUpFrontendRootPage(
            self::ROOT_PAGE_UID,
            ['EXT:typo3_encore/Tests/Functional/Fixtures/Frontend/MainRendererAbsRefPrefix.typoscript']
        );
        $this->setUpSites(self::ROOT_PAGE_UID, []);
        $response = $this->executeFrontendRequest((new InternalRequest())->withPageId(self::ROOT_PAGE_UID));

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

    public function testAddFilesWithHtml5DocType(): void
    {
        $this->setUpFrontendRootPage(
            self::ROOT_PAGE_UID,
            ['EXT:typo3_encore/Tests/Functional/Fixtures/Frontend/MainRendererHtml5.typoscript']
        );
        $this->setUpSites(self::ROOT_PAGE_UID, []);
        $response = $this->executeFrontendRequest((new InternalRequest())->withPageId(self::ROOT_PAGE_UID));

        $content = $response->getBody()
            ->__toString();
        self::assertStringNotContainsString('text/javascript', $content);
    }

    protected function setUpSites(int $pageId, array $sites): void
    {
        if (! isset($sites[$pageId])) {
            $sites[$pageId] = 'EXT:typo3_encore/Tests/Functional/Fixtures/Frontend/site.yaml';
        }

        foreach ($sites as $identifier => $file) {
            $path = Environment::getConfigPath() . '/sites/' . $identifier . '/';
            $target = $path . 'config.yaml';
            if (! file_exists($target)) {
                GeneralUtility::mkdir_deep($path);
                if (! file_exists($file)) {
                    $file = GeneralUtility::getFileAbsFileName($file);
                }
                $fileContent = file_get_contents($file);
                $fileContent = str_replace('\'{rootPageId}\'', (string) $pageId, (string) $fileContent);
                GeneralUtility::writeFile($target, $fileContent);
            }
        }
    }
}
