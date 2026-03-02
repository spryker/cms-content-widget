<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Yves\CmsContentWidget\Plugin\Twig;

use Spryker\Service\Container\ContainerInterface;
use Spryker\Shared\TwigExtension\Dependency\Plugin\TwigPluginInterface;
use Spryker\Yves\CmsContentWidget\Dependency\CmsContentWidgetPluginInterface;
use Spryker\Yves\Kernel\AbstractPlugin;
use Twig\Environment;
use Twig\TwigFunction;

/**
 * @method \Spryker\Yves\CmsContentWidget\CmsContentWidgetFactory getFactory()
 */
class CmsContentWidgetTwigPlugin extends AbstractPlugin implements TwigPluginInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Twig\Environment $twig
     * @param \Spryker\Service\Container\ContainerInterface $container
     *
     * @return \Twig\Environment
     */
    public function extend(Environment $twig, ContainerInterface $container): Environment
    {
        return $this->registerCmsContentWidgets($twig);
    }

    protected function registerCmsContentWidgets(Environment $twig): Environment
    {
        foreach ($this->getFactory()->getCmsContentWidgetPlugins() as $functionName => $cmsContentWidgetPlugin) {
            $twig->addFunction(
                $this->createTwigSimpleFunction($functionName, $cmsContentWidgetPlugin),
            );
        }

        return $twig;
    }

    protected function createTwigSimpleFunction(string $functionName, CmsContentWidgetPluginInterface $cmsContentWidgetPlugin): TwigFunction
    {
        return new TwigFunction(
            $functionName,
            $cmsContentWidgetPlugin->getContentWidgetFunction(),
            $this->getTwigSimpleFunctionOptions(),
        );
    }

    protected function getTwigSimpleFunctionOptions(): array
    {
        return [
            'needs_context' => true,
            'needs_environment' => true,
            'is_safe' => ['html'],
        ];
    }
}
