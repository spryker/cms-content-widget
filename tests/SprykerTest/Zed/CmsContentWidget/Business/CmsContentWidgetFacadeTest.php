<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\CmsContentWidget\Business;

use Codeception\Test\Unit;
use Spryker\Shared\CmsContentWidget\Dependency\CmsContentWidgetConfigurationProviderInterface;
use Spryker\Zed\CmsContentWidget\Business\CmsContentWidgetBusinessFactory;
use Spryker\Zed\CmsContentWidget\Business\CmsContentWidgetFacade;
use Spryker\Zed\CmsContentWidget\CmsContentWidgetConfig;
use Spryker\Zed\CmsContentWidget\CmsContentWidgetDependencyProvider;
use Spryker\Zed\CmsContentWidget\Dependency\Facade\CmsContentWidgetToGlossaryInterface;
use Spryker\Zed\CmsContentWidget\Dependency\Plugin\CmsContentWidgetParameterMapperPluginInterface;
use Spryker\Zed\Kernel\Container;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group CmsContentWidget
 * @group Business
 * @group Facade
 * @group CmsContentWidgetFacadeTest
 * Add your own group annotations below this line
 */
class CmsContentWidgetFacadeTest extends Unit
{
    /**
     * @var \Spryker\Zed\CmsContentWidget\Business\CmsContentWidgetFacade
     */
    protected $cmsContentWidgetFacade;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->cmsContentWidgetFacade = new CmsContentWidgetFacade();
    }

    /**
     * @return void
     */
    public function testMapContentWidgetParametersShouldSkipMappingIfThereIsNoTwigContent(): void
    {
        $parameterMap = $this->cmsContentWidgetFacade->mapContentWidgetParameters('cms content without twig functions');
        $this->assertEmpty($parameterMap);
    }

    /**
     * @return void
     */
    public function testMapContentWidgetParametersShouldMapParametersWithPlugin(): void
    {
        $mockedCmsContentWidgetFunction = $this->createMockedCmsContentWidgetFunction();

        $mockedCmsContentWidgetFunction->method('map')->willReturn([
            'sku1' => 1,
            'sku2' => 2,
        ]);

        $cmsFacade = $this->createCmsFacadeWithMockedContentWidgetParameterMapper($mockedCmsContentWidgetFunction);

        $parameterMap = $cmsFacade->mapContentWidgetParameters("cms content {{ function(['sku1', 'sku2']) }} twig functions.");

        $this->assertArrayHasKey('function', $parameterMap);
        $this->assertCount(2, $parameterMap['function']);
    }

    /**
     * @dataProvider getContentWidgetDataProvider
     *
     * @param string $functionName
     * @param array $availableTemplates
     * @param string $usageInformation
     *
     * @return void
     */
    public function testGetContentWidgetConfigurationListShouldReturnProvidedConfigurations(
        string $functionName,
        array $availableTemplates,
        string $usageInformation
    ): void {
        $cmsContentWidgetConfigurationProviderMock = $this->createCmsContentWidgetConfigurationProviderMock();

        $cmsContentWidgetConfigurationProviderMock
            ->expects($this->once())
            ->method('getFunctionName')
            ->willReturn($functionName);

        $cmsContentWidgetConfigurationProviderMock
            ->expects($this->once())
            ->method('getAvailableTemplates')
            ->willReturn($availableTemplates);

        $cmsContentWidgetConfigurationProviderMock
            ->expects($this->once())
            ->method('getUsageInformation')
            ->willReturn($usageInformation);

        $cmsFacade = $this->createCmsFacadeWithMockedContentWidgetConfigurationProviders($cmsContentWidgetConfigurationProviderMock);

        $cmsContentWidgetConfigurationListTransfer = $cmsFacade->getContentWidgetConfigurationList();
        $this->assertCount(1, $cmsContentWidgetConfigurationListTransfer->getCmsContentWidgetConfigurationList());

        $cmsContentWidgetConfigurationTransfer = $cmsContentWidgetConfigurationListTransfer->getCmsContentWidgetConfigurationList()[0];

        $this->assertSame($functionName, $cmsContentWidgetConfigurationTransfer->getFunctionName());
        $this->assertCount(count($availableTemplates), $cmsContentWidgetConfigurationTransfer->getTemplates());

        $mappedTemplates = $cmsContentWidgetConfigurationTransfer->getTemplates();
        foreach ($availableTemplates as $identifier => $templatePath) {
            $this->assertArrayHasKey($identifier, $mappedTemplates);
            $this->assertEquals($mappedTemplates[$identifier], $templatePath);
        }

        $this->assertSame($usageInformation, $cmsContentWidgetConfigurationTransfer->getUsageInformation());
    }

    /**
     * @return void
     */
    public function testExpandCmsBlockCollectorDataDoesNotThrowExceptionForNonExistingGlossaryTranslationKey(): void
    {
        // Arrange
        $cmsContentWidgetFacade = $this->createCmsContentWidgetFacade();

        // Assert
        $this->expectNotToPerformAssertions();

        // Act
        $cmsContentWidgetFacade->expandCmsBlockCollectorData(
            [
                'placeholders' => [
                    'content' => 'this_key_does_not_exist',
                ],
            ],
            $this->tester->buildLocaleTransferObject(),
        );
    }

    /**
     * @return array
     */
    public function getContentWidgetDataProvider(): array
    {
        return [
            [
               'functionName' => 'functionName',
               'availableTemplates' => [
                   'identifier' => '@module/path/to/template.twig',
               ],
               'usageInformation' => 'how to..',
            ],
            [
                'functionName' => 'functionName1',
                'availableTemplates' => [
                    'identifier1' => '@module/path/to/template1.twig',
                    'identifier2' => '@module/path/to/template2.twig',
                ],
                'usageInformation' => 'how to..2',
            ],
        ];
    }

    /**
     * @param \Spryker\Shared\CmsContentWidget\Dependency\CmsContentWidgetConfigurationProviderInterface $cmsContentWidgetConfigurationProviderMock
     *
     * @return \Spryker\Zed\CmsContentWidget\Business\CmsContentWidgetFacade
     */
    protected function createCmsFacadeWithMockedContentWidgetConfigurationProviders(
        CmsContentWidgetConfigurationProviderInterface $cmsContentWidgetConfigurationProviderMock
    ): CmsContentWidgetFacade {
        $cmsContentWidgetFacade = $this->createCmsContentWidgetFacade();
        $cmsBusinessFactory = $this->createBusinessFactory();

        $cmsConfigMock = $this->createCmsConfigMock();
        $cmsConfigMock->method('getCmsContentWidgetConfigurationProviders')->willReturn([
            'function' => $cmsContentWidgetConfigurationProviderMock,
        ]);

        $cmsBusinessFactory->setConfig($cmsConfigMock);
        $cmsContentWidgetFacade->setFactory($cmsBusinessFactory);

        return $cmsContentWidgetFacade;
    }

    /**
     * @param \Spryker\Zed\CmsContentWidget\Dependency\Plugin\CmsContentWidgetParameterMapperPluginInterface $cmsContentWidgetParameterMapperPluginMock
     *
     * @return \Spryker\Zed\CmsContentWidget\Business\CmsContentWidgetFacade
     */
    protected function createCmsFacadeWithMockedContentWidgetParameterMapper(
        CmsContentWidgetParameterMapperPluginInterface $cmsContentWidgetParameterMapperPluginMock
    ): CmsContentWidgetFacade {
        $cmsContentFacade = $this->createCmsContentWidgetFacade();
        $cmsBusinessFactory = $this->createBusinessFactory();

        $container = $this->createZedContainer();
        $container[CmsContentWidgetDependencyProvider::PLUGINS_CMS_CONTENT_WIDGET_PARAMETER_MAPPERS] = function (Container $container) use ($cmsContentWidgetParameterMapperPluginMock) {
            return [
              'function' => $cmsContentWidgetParameterMapperPluginMock,
            ];
        };

        $container[CmsContentWidgetDependencyProvider::FACADE_GLOSSARY] = function (Container $container) {
            return $this->createGlossaryFacadeMock();
        };

        $cmsBusinessFactory->setContainer($container);

        $cmsContentFacade->setFactory($cmsBusinessFactory);

        return $cmsContentFacade;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\CmsContentWidget\Dependency\Plugin\CmsContentWidgetParameterMapperPluginInterface
     */
    protected function createMockedCmsContentWidgetFunction(): CmsContentWidgetParameterMapperPluginInterface
    {
        return $this->getMockBuilder(CmsContentWidgetParameterMapperPluginInterface::class)
            ->getMock();
    }

    /**
     * @return \Spryker\Zed\CmsContentWidget\Business\CmsContentWidgetFacade
     */
    protected function createCmsContentWidgetFacade(): CmsContentWidgetFacade
    {
        return new CmsContentWidgetFacade();
    }

    /**
     * @return \Spryker\Zed\CmsContentWidget\Business\CmsContentWidgetBusinessFactory
     */
    protected function createBusinessFactory(): CmsContentWidgetBusinessFactory
    {
        return new CmsContentWidgetBusinessFactory();
    }

    /**
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function createZedContainer(): Container
    {
        return new Container();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\CmsContentWidget\CmsContentWidgetConfig
     */
    protected function createCmsConfigMock(): CmsContentWidgetConfig
    {
        return $this->getMockBuilder(CmsContentWidgetConfig::class)->getMock();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Shared\CmsContentWidget\Dependency\CmsContentWidgetConfigurationProviderInterface
     */
    protected function createCmsContentWidgetConfigurationProviderMock(): CmsContentWidgetConfigurationProviderInterface
    {
        return $this->getMockBuilder(CmsContentWidgetConfigurationProviderInterface::class)->getMock();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\CmsContentWidget\Dependency\Facade\CmsContentWidgetToGlossaryInterface
     */
    protected function createGlossaryFacadeMock(): CmsContentWidgetToGlossaryInterface
    {
        return $this->getMockBuilder(CmsContentWidgetToGlossaryInterface::class)->getMock();
    }
}
