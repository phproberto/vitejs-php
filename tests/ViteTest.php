<?php

namespace Phproberto\Vite\Tests;

use Phproberto\Vite\Vite;
use Phproberto\Vite\ViteEntryConfiguration;
use PHPUnit\Framework\TestCase;

class ViteTest extends TestCase
{
    /**
     * @var Vite
     */
    protected $vite;

    public function setUp(): void
    {
        $config =[
            'mode' => array_rand(['production', 'development']),
        ];

        $this->vite = new Vite(new ViteEntryConfiguration($config));
    }

    public function tearDown(): void
    {
        $this->vite = null;
    }

    public function entriesInTextDataProvider(): array
    {
        $basePaths = [
            'app' => __DIR__ . '/stubs/app',
        ];

        return [
            [
                'text'   => 'aaaaaaa `<br/> @vite("src/main.js", {"basePath": "' . $basePaths['app'] . '"}) more lines after',
                'path'   => 'src/main.js',
                'config' => [
                    'basePath' => $basePaths['app'],
                ],
                'tag'    => '@vite("src/main.js", {"basePath": "' . $basePaths['app'] . '"})',
                'expectedText' => "aaaaaaa `<br/> <script type=\"module\" crossorigin=\"anonymous\" src=\"http://localhost:5173/src/main.js\"></script>\n\n more lines after"
            ],
            [
                'text'   => 'aaaaaaa `<br/> @vite("src/main.js", {"internalHost": "http://phproberto.com", "basePath": "' . $basePaths['app'] . '"}) more lines after',
                'path'   => 'src/main.js',
                'config' => [
                    'internalHost' => 'http://phproberto.com',
                    'basePath' => __DIR__ . '/stubs/app',
                ],
                'tag'    => '@vite("src/main.js", {"internalHost": "http://phproberto.com", "basePath": "' . $basePaths['app'] . '"})',
                'expectedText' => "aaaaaaa `<br/> <script type=\"module\" crossorigin=\"anonymous\" src=\"http://localhost:5173/src/main.js\"></script>\n\n more lines after"
            ],
            [
                'text'   => '<div class="test">something</div> `<br/> @vite("src/main.js", {"externalHost": "http://localhost:5195", "basePath": "' . $basePaths['app'] . '"}) more lines after',
                'path'   => 'src/main.js',
                'config' => [
                    'externalHost' => 'http://localhost:5195',
                    'basePath' => $basePaths['app'],
                ],
                'tag'    => '@vite("src/main.js", {"externalHost": "http://localhost:5195", "basePath": "' . $basePaths['app'] . '"})',
                'expectedText' => "<div class=\"test\">something</div> `<br/> <script type=\"module\" crossorigin=\"anonymous\" src=\"http://localhost:5195/src/main.js\"></script>\n\n more lines after"
            ],
            [
                'text'   => 'aaaaaaa `<br/>ss@vite("src/main.js", {"baseUrl": "http://phproberto.com", "basePath": "' . $basePaths['app'] . '"}) more lines after',
                'path'   => 'src/main.js',
                'config' => [
                    'baseUrl' => 'http://phproberto.com',
                    'basePath' => $basePaths['app'],
                ],
                'tag'    => '@vite("src/main.js", {"baseUrl": "http://phproberto.com", "basePath": "' . $basePaths['app'] . '"})',
                'expectedText' => "aaaaaaa `<br/>ss<script type=\"module\" crossorigin=\"anonymous\" src=\"http://localhost:5173/src/main.js\"></script>\n\n more lines after"
            ],
            [
                'text'   => 'aaaaaaa `<br/> @vite("src/main.js", {"mode": "development", "basePath": "' . $basePaths['app'] . '"}) more lines after',
                'path'   => 'src/main.js',
                'config' => [
                    'mode' => 'development',
                    'basePath' => $basePaths['app'],
                ],
                'tag'    => '@vite("src/main.js", {"mode": "development", "basePath": "' . $basePaths['app'] . '"})',
                'expectedText' => "aaaaaaa `<br/> <script type=\"module\" crossorigin=\"anonymous\" src=\"http://localhost:5173/src/main.js\"></script>\n\n more lines after"
            ],
        ];
    }

    public function testReplaceTagsInText()
    {
        $vite = new Vite();
        $this->assertEquals('Hello, World!', $vite->replaceTagsInText('Hello, World!'));
    }

    /**
     * @dataProvider entriesInTextDataProvider
     */
    public function testGetEntriesFromText($text, $path, $config, $match)
    {
        $globalConfig =[
            'mode' => array_rand(['production', 'development']),
        ];

        $vite = new Vite(new ViteEntryConfiguration($globalConfig));

        $expectedConfig = array_merge($globalConfig, $config);

        if (!$path) {
            $this->assertEquals([], $vite->getEntriesFromText($text));
            return;
        }

        $entries = $vite->getEntriesFromText($text);

        $this->assertCount(1, $entries);

        $this->assertEquals($match, $entries[0]->getTag());
        $this->assertEquals($path, $entries[0]->getPath());
        $this->assertEquals($expectedConfig, $entries[0]->getConfig()->getRawData());
    }

    /**
     * @dataProvider entriesInTextDataProvider
     */
    public function testReplacingTagsInText($text, $path, $config, $match, $expectedText = '')
    {
        $vite = new Vite();

        $this->assertEquals($expectedText, $vite->replaceTagsInText($text));

        if (!$path) {
            $this->assertEquals([], $vite->getEntriesFromText($text));
            return;
        }
    }
}