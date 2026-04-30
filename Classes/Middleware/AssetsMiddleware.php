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
use Psr\Link\LinkProviderInterface;
use Ssch\Typo3Encore\Integration\AssetRegistryInterface;
use Ssch\Typo3Encore\Integration\SettingsServiceInterface;
use Ssch\Typo3Encore\Service\CacheService;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\WebLink\GenericLinkProvider;
use Symfony\Component\WebLink\HttpHeaderSerializer;
use Symfony\Component\WebLink\Link;
use TYPO3\CMS\Core\Cache\CacheDataCollector;
use TYPO3\CMS\Core\Http\NullResponse;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use UnexpectedValueException;

final class AssetsMiddleware implements MiddlewareInterface
{
    private static array $crossOriginAllowed = ['preload', 'preconnect'];

    public function __construct(
        private readonly AssetRegistryInterface $assetRegistry,
        private readonly SettingsServiceInterface $settingsService,
        private CacheService $cacheService,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        if (($response instanceof NullResponse)) {
            return $response;
        }
        $cacheData = $this->cacheService->get($request->getAttribute('frontend.cache.collector'));

        $registeredFiles = $this->collectRegisteredFiles($cacheData);

        if ([] === $registeredFiles) {
            return $response;
        }

        $linkProvider = $request->getAttribute('_links');
        if (! $linkProvider instanceof LinkProviderInterface) {
            $linkProvider = new GenericLinkProvider();
        }

        $defaultAttributes = $this->collectDefaultAttributes($cacheData);
        $crossOrigin = (bool) ($defaultAttributes['crossorigin'] ?? false);

        foreach ($registeredFiles as $rel => $relFiles) {
            // You can disable or enable one of the resource hints via typoscript simply by adding something like that preload.enable = 1, dns-prefetch.enable = 1
            if (false === $this->getBooleanConfigByPath(sprintf('%s.enable', $rel), $cacheData)) {
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

        if ([] !== $linkProvider->getLinks()) {
            $links = $linkProvider->getLinks();
            /** @phpstan-ignore argument.type */
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
        return false !== $crossOrigin && in_array($rel, self::$crossOriginAllowed, true);
    }

    private function collectRegisteredFiles(array $cacheData): array
    {
        $registeredFiles = $cacheData['registered_files'] ?? [];
        return array_replace($registeredFiles, $this->assetRegistry->getRegisteredFiles());
    }

    private function collectDefaultAttributes(array $cacheData): array
    {
        $defaultAttributes = $cacheData['default_attributes'] ?? [];
        return array_replace($defaultAttributes, $this->assetRegistry->getDefaultAttributes());
    }

    private function getBooleanConfigByPath(string $path, array $cacheData): bool
    {
        if ([] !== $this->settingsService->getSettings()) {
            return $this->settingsService->getBooleanByPath($path);
        }
        $cachedSettings = $cacheData['settings'] ?? [];
        return (bool) ObjectAccess::getPropertyPath($cachedSettings, $path);
    }
}
