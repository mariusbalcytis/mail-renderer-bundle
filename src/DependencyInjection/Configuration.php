<?php

namespace Maba\Bundle\MailRendererBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('maba_mail_renderer');

        $cssAssetsNode = $rootNode->children()->arrayNode('css_assets')->children();
        $cssAssetsNode->scalarNode('inline')->defaultNull();
        $cssAssetsNode->scalarNode('imports')->defaultNull();

        $fromNode = $rootNode->children()->arrayNode('from')->children();
        $fromNode->scalarNode('email')->defaultNull();
        $fromNode->scalarNode('name')->defaultNull();

        return $treeBuilder;
    }
}
