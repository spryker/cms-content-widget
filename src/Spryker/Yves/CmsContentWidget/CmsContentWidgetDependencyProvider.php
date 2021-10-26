<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Yves\CmsContentWidget;

use Spryker\Shared\Kernel\ContainerInterface;
use Spryker\Yves\Kernel\AbstractBundleDependencyProvider;
use Spryker\Yves\Kernel\Container;
use Spryker\Yves\Kernel\Plugin\Pimple;

/**
 * @method \Spryker\Yves\CmsContentWidget\CmsContentWidgetConfig getConfig()
 */
class CmsContentWidgetDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const CMS_CONTENT_WIDGET_PLUGINS = 'CMS CONTENT WIDGET PLUGINS';

    /**
     * @deprecated Will be removed without replacement.
     *
     * @var string
     */
    public const TWIG_ENVIRONMENT = 'TWIG ENVIRONMENT';

    /**
     * @uses \Spryker\Yves\Twig\Plugin\Application\TwigApplicationPlugin::SERVICE_TWIG
     *
     * @var string
     */
    public const SERVICE_TWIG = 'twig';

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    public function provideDependencies(Container $container)
    {
        $container->set(static::CMS_CONTENT_WIDGET_PLUGINS, function (Container $container) {
            return $this->getCmsContentWidgetPlugins();
        });

        $container->set(static::SERVICE_TWIG, function (ContainerInterface $container) {
            return $container->getApplicationService(static::SERVICE_TWIG);
        });

        return $container;
    }

    /**
     * @deprecated Will be removed without replacement.
     *
     * @return \Twig\Environment
     */
    protected function getTwigEnvironment()
    {
        $pimplePlugin = new Pimple();
        $twig = $pimplePlugin->getApplication()->get(static::SERVICE_TWIG);

        return $twig;
    }

    /**
     * Returns list of cms content widget plugins which are twig functions used in cms content pages/blocks.
     * Should return key value pair where key is function name and value is concrete content widget plugin.
     *
     * @return array<\Spryker\Yves\CmsContentWidget\Dependency\CmsContentWidgetPluginInterface>
     */
    public function getCmsContentWidgetPlugins()
    {
        return [];
    }
}
