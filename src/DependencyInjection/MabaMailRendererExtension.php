<?php

namespace Maba\Bundle\MailRendererBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class MabaMailRendererExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        if (isset($config['css_assets']['inline'])) {
            $container->setParameter('maba_mail_renderer.filter.inline_css.asset', $config['css_assets']['inline']);
            $container->getDefinition('maba_mail_renderer.formatted_message_mailer')
                ->addMethodCall('addHtmlFilter', array(
                    new Reference('maba_mail_renderer.filter.inline_css')
                ))
            ;
        }

        if (isset($config['css_assets']['imports'])) {
            $container->setParameter('maba_mail_renderer.filter.styles_tag.asset', $config['css_assets']['imports']);
            $container->getDefinition('maba_mail_renderer.formatted_message_mailer')
                ->addMethodCall('addHtmlFilter', array(
                    new Reference('maba_mail_renderer.filter.styles_tag')
                ))
            ;
        }

        $container->setParameter('maba_mail_renderer.formatted_message_mailer.from_email', $config['from']['email']);
        $container->setParameter('maba_mail_renderer.formatted_message_mailer.from_name', $config['from']['name']);
    }
}
