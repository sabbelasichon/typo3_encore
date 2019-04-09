<?php
declare(strict_types=1);


namespace Ssch\Typo3Encore\Aspect;


use Ssch\Typo3Encore\Asset\JsonManifestVersionStrategy;
use TYPO3\CMS\Core\Resource\ResourceFactory;

final class ResourceFactorySlot
{
    /**
     * @var JsonManifestVersionStrategy
     */
    private $jsonManifestVersionStrategy;

    /**
     * ResourceFactorySlot constructor.
     *
     * @param JsonManifestVersionStrategy $JsonManifestVersionStrategy
     */
    public function __construct(JsonManifestVersionStrategy $JsonManifestVersionStrategy)
    {
        $this->jsonManifestVersionStrategy = $JsonManifestVersionStrategy;
    }

    /**
     * @param ResourceFactory $resourceFactory
     * @param int $uid
     * @param array $recordData
     * @param string|null $fileIdentifier
     * @param string|null $slotName
     *
     * @return array
     */
    public function jsonManifestVersionStrategy(ResourceFactory $resourceFactory, int $uid, array $recordData, string $fileIdentifier = null, string $slotName = null): array
    {
        return [$resourceFactory, $uid, $recordData, $this->jsonManifestVersionStrategy->getVersion($fileIdentifier)];
    }

}