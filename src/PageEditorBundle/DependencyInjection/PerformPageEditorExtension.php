<?php

namespace Perform\PageEditorBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Perform\Licensing\Licensing;
use Perform\BaseBundle\DependencyInjection\Assets;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PerformPageEditorExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        Licensing::validateProject($container);
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('perform_page_editor.fixture_definitions', $config['fixtures']);
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('perform_page_editor.toolbar.excluded_urls', $config['toolbar']['excluded_urls']);

        if (class_exists(Assets::class)) {
            Assets::addNamespace($container, 'perform-page-editor', __DIR__.'/../Resources');
            Assets::addNpmConfig($container, __DIR__.'/../package.json');
            Assets::addEntryPoint($container, 'perform-page-editor', [
                __DIR__.'/../Resources/scss/perform-page-editor.scss',
                __DIR__.'/../Resources/js/perform-page-editor.js'
            ]);
        }
    }
}