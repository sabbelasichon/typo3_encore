<?php
declare(strict_types = 1);

namespace Ssch\Typo3Encore\Tests\Functional;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\TestingFramework\Core\Exception;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class IncludeFilesTest extends FunctionalTestCase
{
    /**
     * @var int
     */
    private const ROOT_PAGE_UID = 1;

    /**
     * @var array
     */
    protected $testExtensionsToLoad = [
        'typo3conf/ext/typo3_encore',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        try {
            $this->importDataSet(__DIR__ . '/Fixtures/pages.xml');
        } catch (Exception $e) {
        }
    }

    /**
     * @test
     */
    public function addFiles(): void
    {
        $this->setUpFrontendRootPage(
            self::ROOT_PAGE_UID,
            [
                'typo3conf/ext/typo3_encore/Tests/Functional/Fixtures/Frontend/MainRenderer.typoscript',
            ]
        );

        $response = $this->executeFrontendRequest((new InternalRequest())->withPageId(self::ROOT_PAGE_UID));

        $content = $response->getBody()->getContents();
        $this->assertStringContainsString('TYPO3 Webpack Encore - Modern Frontend Development', $content);
        $this->assertStringContainsString('main.css', $content);
        $this->assertStringContainsString('main.js', $content);
        $this->assertStringContainsString('sha384-ysKW+jP4sNH9UfX9+fqN4iC/RB3L9jmWUd8ABJrBbAHFwL6wNmvNT5x178Fx6Xh0', $content);
    }
}
