<?php
declare(strict_types=1);


namespace Ssch\Typo3Encore\Integration;


use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;

final class SettingsService implements SettingsServiceInterface
{

    /**
     * @var array
     */
    private $settings;

    /**
     * @var ConfigurationManagerInterface
     */
    private $configurationManager;

    /**
     * SettingsService constructor.
     *
     * @param ConfigurationManagerInterface $configurationManager
     */
    public function __construct(ConfigurationManagerInterface $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }


    /**
     * Returns all settings.
     *
     * @return array
     */
    public function getSettings(): array
    {
        if ($this->settings === null) {
            $this->settings = $this->configurationManager->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
                'Typo3Encore'
            );
        }

        return $this->settings;
    }

    /**
     * Returns the settings at path $path, which is separated by ".",
     * e.g. "pages.uid".
     * "pages.uid" would return $this->settings['pages']['uid'].
     *
     * If the path is invalid or no entry is found, false is returned.
     *
     * @param string $path
     *
     * @return mixed
     */
    public function getByPath(string $path)
    {
        return ObjectAccess::getPropertyPath($this->getSettings(), $path);
    }
}