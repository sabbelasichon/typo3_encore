<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Link\LinkInterface;
use Ssch\Typo3Encore\Integration\AssetRegistryInterface;
use Ssch\Typo3Encore\Integration\SettingsServiceInterface;
use Symfony\Component\WebLink\GenericLinkProvider;
use Symfony\Component\WebLink\HttpHeaderSerializer;
use Symfony\Component\WebLink\Link;
use Traversable;
use TYPO3\CMS\Core\Http\NullResponse;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use UnexpectedValueException;

final class AssetsMiddleware implements MiddlewareInterface
{
    private static array $crossOriginAllowed = ['preload', 'preconnect'];

    /**
     * @var TypoScriptFrontendController
     */
    private $controller;

    private AssetRegistryInterface $assetRegistry;

    private SettingsServiceInterface $settingsService;

    public function __construct(AssetRegistryInterface $assetRegistry, SettingsServiceInterface $settingsService)
    {
        $this->controller = $GLOBALS['TSFE'];
        $this->settingsService = $settingsService;
        $this->assetRegistry = $assetRegistry;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        if (($response instanceof NullResponse)) {
            return $response;
        }

        $registeredFiles = $this->collectRegisteredFiles();

        if ([] === $registeredFiles) {
            return $response;
        }

        $linkProvider = $request->getAttribute('_links');
        if (null === $linkProvider) {
            $request = $request->withAttribute('_links', new GenericLinkProvider());
        }

        /** @var GenericLinkProvider $linkProvider */
        $linkProvider = $request->getAttribute('_links');
        $defaultAttributes = $this->collectDefaultAttributes();
        $crossOrigin = $defaultAttributes['crossorigin'] ? (bool) $defaultAttributes['crossorigin'] : false;

        foreach ($registeredFiles as $rel => $relFiles) {
            // You can disable or enable one of the resource hints via typoscript simply by adding something like that preload.enable = 1, dns-prefetch.enable = 1
            if (false === $this->getBooleanConfigByPath(sprintf('%s.enable', $rel))) {
                continue;
            }

            foreach ($relFiles['files'] as $type => $files) {
                foreach ($files as $href => $attributes) {
                    $link = (new Link($rel, PathUtility::getAbsoluteWebPath($href)))->withAttribute('as', $type);
                    if ($this->canAddCrossOriginAttribute($crossOrigin, $rel)) {
                        $link = $link->withAttribute('crossorigin', $crossOrigin);
                    }

                    foreach ($attributes as $key => $value) {
                        $link = $link->withAttribute($key, $value);
                    }

                    $linkProvider = $linkProvider->withLink($link);
                }
            }
        }

        $request = $request->withAttribute('_links', $linkProvider);

        /** @var GenericLinkProvider $linkProvider */
        $linkProvider = $request->getAttribute('_links');

        if ([] !== $linkProvider->getLinks()) {
            /** @var LinkInterface[]|Traversable $links */
            $links = $linkProvider->getLinks();
            $serializedLinks = (new HttpHeaderSerializer())->serialize($links);

            if (! is_string($serializedLinks)) {
                throw new UnexpectedValueException('Could not serialize the links');
            }

            $response = $response->withHeader('Link', $serializedLinks);
        }

        return $response;
    }

    private function canAddCrossOriginAttribute(bool $crossOrigin, string $rel): bool
    {
        return false !== $crossOrigin && '' !== (string) $crossOrigin && in_array(
            $rel,
            self::$crossOriginAllowed,
            true
        );
    }

    private function collectRegisteredFiles(): array
    {
        return array_replace(
            $this->controller->config['encore_asset_registry']['registered_files'] ?? [],
            $this->assetRegistry->getRegisteredFiles()
        );
    }

    private function collectDefaultAttributes(): array
    {
        return array_replace(
            $this->controller->config['encore_asset_registry']['default_attributes'] ?? [],
            $this->assetRegistry->getDefaultAttributes()
        );
    }

    private function getBooleanConfigByPath(string $path): bool
    {
        if ([] !== $this->settingsService->getSettings()) {
            return $this->settingsService->getBooleanByPath($path);
        }

        $cachedSettings = $this->controller->config['encore_asset_registry']['settings'] ?? [];

        return (bool) ObjectAccess::getPropertyPath($cachedSettings, $path);
    }
}
