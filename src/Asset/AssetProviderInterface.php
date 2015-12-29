<?php

namespace Maba\Bundle\MailRendererBundle\Asset;

interface AssetProviderInterface
{

    /**
     * @param string $assetName
     * @return string contents of asset
     */
    public function getAssetContent($assetName);
}
