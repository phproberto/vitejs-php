<?php

namespace Phproberto\Vite\Tests;

use Phproberto\Vite\ViteEntry;
use Phproberto\Vite\Tests\BaseTestCase;
use Phproberto\Vite\ViteEntryConfiguration;

class ViteEntryTest extends BaseTestCase
{
    public function testItUsesADefaultConfigIfNoneProvided()
    {
        $entry = new ViteEntry('src/main.js');

        $this->assertInstanceOf(ViteEntryConfiguration::class, $entry->getConfig());
    }

    public function testItLoadsSpecifiedConfig()
    {
        $config = new ViteEntryConfiguration();

        $entry = new ViteEntry('src/main.js', $config);

        $this->assertSame($config, $entry->getConfig());
    }

    public function testIsDevReturnsFalseIfProduction()
    {
        $config = new ViteEntryConfiguration([
            'mode' => 'production',
        ]);

        $entry = new ViteEntry('src/main.js', $config);

        $this->assertFalse($entry->isDev());
    }

    public function testIsDevReturnsCachedValueIfExists()
    {
        $basePath = $this->getFixturePath('app');
        $config = new ViteEntryConfiguration([
            'basePath' => $basePath,
        ]);

        $manifestPath = $this->getFixturePath('app/manifest.json');

        $entry = $this->getMockBuilder(ViteEntry::class)
            ->setConstructorArgs(['src/main.jsp', $config])
            ->setMethods(['checkIfActiveViteServer'])
            ->getMock();

        $entry->expects($this->never())
            ->method('checkIfActiveViteServer');

        $this->setStaticPropertyValue(
            ViteEntry::class,
            'checked',
            [$manifestPath => false]
        );

        $this->assertFalse($entry->isDev());

        $this->setStaticPropertyValue(
            ViteEntry::class,
            'checked',
            [$manifestPath => true]
        );

        $this->assertTrue($entry->isDev());
    }
}