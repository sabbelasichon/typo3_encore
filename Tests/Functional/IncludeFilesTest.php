<?php
declare(strict_types=1);


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

use Nimut\TestingFramework\Exception\Exception;
use Nimut\TestingFramework\TestCase\FunctionalTestCase;

final class IncludeFilesTest extends FunctionalTestCase
{
    private const ROOT_PAGE_UID = 1;

    /**
     * @var array
     */
    protected $testExtensionsToLoad = [
        'typo3conf/ext/typo3_encore',
    ];

    protected function setUp()
    {
        parent::setUp();
        try {
            $this->importDataSet(__DIR__.'/Fixtures/pages.xml');
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
        $response = $this->getFrontendResponse(
            self::ROOT_PAGE_UID
        );

        $this->assertStringContainsString('TYPO3 Webpack Encore - Modern Frontend Development', $response->getContent());
        $this->assertStringContainsString('main.css', $response->getContent());
        $this->assertStringContainsString('main.js', $response->getContent());
    }
}
