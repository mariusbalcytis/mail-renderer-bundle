<?php

namespace Maba\Bundle\MailRendererBundle\Filter;

use Maba\Bundle\MailRendererBundle\Asset\AssetProviderInterface;
use Maba\Bundle\MailRendererBundle\InlineCss\StyleToInlineConverter;

class InlineCssFilter implements FilterInterface
{
    protected $styleToInlineConverter;
    protected $assetProvider;

    /**
     * @var string name of asset to include in emails inlined as styles
     */
    protected $cssAsset;


    /**
     * @param AssetProviderInterface $assetProvider
     * @param StyleToInlineConverter $styleToInlineConverter
     * @param string $cssAsset
     */
    public function __construct(
        AssetProviderInterface $assetProvider,
        StyleToInlineConverter $styleToInlineConverter,
        $cssAsset
    ) {
        $this->assetProvider = $assetProvider;
        $this->styleToInlineConverter = $styleToInlineConverter;
        $this->cssAsset = $cssAsset;
    }

    public function filter($content)
    {
        $css = $this->assetProvider->getAssetContent($this->cssAsset);
        return $this->styleToInlineConverter->inlineCSS($content, $css);
    }
}
