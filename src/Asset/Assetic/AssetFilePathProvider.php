<?php

namespace Maba\Bundle\MailRendererBundle\Asset\Assetic;

class AssetFilePathProvider
{
    protected $cacheDir;

    /**
     * @param string $cacheDir
     */
    public function __construct($cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }

    public function getAssetFilePath($assetName, $cacheDir = null)
    {
        $baseDir = $cacheDir !== null ? $cacheDir : $this->cacheDir;

        $directory = $baseDir . DIRECTORY_SEPARATOR . 'maba-mail-renderer';
        if (!is_dir($directory)) {
            mkdir($directory);
        }

        $filename = preg_replace('/[^a-z_\-0-9]/i', '', $assetName) . '-' . sha1($assetName);
        return $directory . DIRECTORY_SEPARATOR . $filename;
    }
}
