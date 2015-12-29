<?php

namespace Maba\Bundle\MailRendererBundle\Filter;

use Maba\Bundle\MailRendererBundle\Asset\AssetProviderInterface;

class StylesTagFilter implements FilterInterface
{
    protected $assetProvider;

    /**
     * @var string name of asset to include in emails in <style> tag
     */
    protected $cssAsset;


    /**
     * @param AssetProviderInterface $assetProvider
     * @param string $cssAsset
     */
    public function __construct(
        AssetProviderInterface $assetProvider,
        $cssAsset
    ) {
        $this->assetProvider = $assetProvider;
        $this->cssAsset = $cssAsset;
    }

    public function filter($content)
    {
        $css = $this->assetProvider->getAssetContent($this->cssAsset);
        $inlineStyles = '<style>' . htmlspecialchars($css, ENT_QUOTES, 'UTF-8') . '</style>';
        $i = strpos($content, '</head>');
        if ($i === false) {
            throw new \RuntimeException('</head> not found in resulting HTML');
        }
        return substr($content, 0, $i) . $inlineStyles . substr($content, $i);
    }
}
