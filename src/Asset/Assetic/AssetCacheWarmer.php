<?php

namespace Maba\Bundle\MailRendererBundle\Asset\Assetic;

use Assetic\AssetManager;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

class AssetCacheWarmer implements CacheWarmerInterface
{
    protected $assetManager;
    protected $assetFilePathProvider;
    protected $assets;

    /**
     * @param AssetManager $assetManager
     * @param AssetFilePathProvider $assetFilePathProvider
     * @param array $assets array of asset names
     */
    public function __construct(AssetManager $assetManager, AssetFilePathProvider $assetFilePathProvider, array $assets)
    {
        $this->assetManager = $assetManager;
        $this->assetFilePathProvider = $assetFilePathProvider;
        $this->assets = $assets;
    }

    /**
     * This cache warmer is not optional - files must exist, as asset provider takes them directly from cache
     *
     * @return bool
     */
    public function isOptional()
    {
        return false;
    }

    public function warmUp($cacheDir)
    {
        foreach (array_filter($this->assets) as $assetName) {
            file_put_contents(
                $this->assetFilePathProvider->getAssetFilePath($assetName, $cacheDir),
                $this->assetManager->get($assetName)->dump()
            );
        }
    }
}
