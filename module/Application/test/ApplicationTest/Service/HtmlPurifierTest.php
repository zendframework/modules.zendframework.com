<?php

namespace ApplicationTest\Integration\View\Helper;

use Application\Service\HtmlPurifierFactory;
use PHPUnit_Framework_TestCase;
use Zend\ServiceManager\ServiceManager;

class HtmlPurifierTest extends PHPUnit_Framework_TestCase
{
    public function testConfigCanBePassedToService()
    {
        $config = [
            'htmlpurifier' => [
                'HTML.AllowedElements' => 'foo'
            ]
        ];

        $serviceMock = $this->getMock(ServiceManager::class, ['get']);
        $serviceMock
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo('Config'))
            ->willReturn($config);

        $factory = new HtmlPurifierFactory();
        $htmlPurifierInstance = $factory->createService($serviceMock);

        $this->assertArrayHasKey('foo', $htmlPurifierInstance->config->get('HTML.AllowedElements'));
    }
}
