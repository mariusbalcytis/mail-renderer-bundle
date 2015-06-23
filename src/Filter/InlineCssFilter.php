<?php

namespace Maba\Bundle\MailRendererBundle\Filter;

use Assetic\AssetManager;
use Maba\Bundle\MailRendererBundle\InlineCss\StyleToInlineConverter;

class InlineCssFilter implements FilterInterface
{
    /**
     * @var StyleToInlineConverter
     */
    protected $styleToInlineConverter;

    /**
     * @var AssetManager
     */
    protected $assetManager;

    /**
     * @var string name of asset to include in emails inlined as styles
     */
    protected $cssAsset;


    /**
     * @param AssetManager $assetManager
     * @param StyleToInlineConverter $styleToInlineConverter
     * @param string $cssAsset
     */
    public function setAssets(
        AssetManager $assetManager,
        StyleToInlineConverter $styleToInlineConverter,
        $cssAsset
    ) {
        $this->assetManager = $assetManager;
        $this->styleToInlineConverter = $styleToInlineConverter;
        $this->cssAsset = $cssAsset;
    }

    public function filter($content)
    {
        $css = $this->assetManager->get($this->cssAsset)->dump();
        return $this->styleToInlineConverter->inlineCSS($content, $css);
    }
}
