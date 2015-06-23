<?php

namespace Maba\Bundle\MailRendererBundle\Filter;

use Assetic\AssetManager;

class StylesTagFilter implements FilterInterface
{
    /**
     * @var AssetManager
     */
    protected $assetManager;

    /**
     * @var string name of asset to include in emails in <style> tag
     */
    protected $cssAsset;


    /**
     * @param AssetManager $assetManager
     * @param string $cssAsset
     */
    public function __construct(
        AssetManager $assetManager,
        $cssAsset
    ) {
        $this->assetManager = $assetManager;
        $this->cssAsset = $cssAsset;
    }

    public function filter($content)
    {
        $css = $this->assetManager->get($this->cssAsset)->dump();
        $inlineStyles = '<style>' . htmlspecialchars($css, ENT_QUOTES, 'UTF-8') . '</style>';
        $i = strpos($content, '</head>');
        if ($i === false) {
            throw new \RuntimeException('</head> not found in resulting HTML');
        }
        return substr($content, 0, $i) . $inlineStyles . substr($content, $i);
    }
}
