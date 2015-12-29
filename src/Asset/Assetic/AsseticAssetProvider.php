<?php

namespace Maba\Bundle\MailRendererBundle\Asset\Assetic;

use Maba\Bundle\MailRendererBundle\Asset\AssetProviderInterface;

/**
 * Depends on AssetCacheWarmer as it generates asset files in the right place
 */
class AsseticAssetProvider implements AssetProviderInterface
{
    protected $assetFilePathProvider;

    public function __construct(AssetFilePathProvider $assetFilePathProvider)
    {
        $this->assetFilePathProvider = $assetFilePathProvider;
    }

    public function getAssetContent($assetName)
    {
        $filePath = $this->assetFilePathProvider->getAssetFilePath($assetName);

        if (!file_exists($filePath)) {
            throw new \RuntimeException(
                sprintf('Asset file not found (%s), probably cache warmer was not run', $filePath)
            );
        }
        if (!is_readable($filePath)) {
            throw new \RuntimeException(
                sprintf('Asset file not readable (%s)', $filePath)
            );
        }

        return file_get_contents($filePath);
    }
}
